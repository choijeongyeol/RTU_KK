
[태양광 발전량]

< 오늘 발전량 >
* 단위 : f.cid단위로 출력.  7라인 
SELECT 
    DATE_FORMAT(MAX(s.rdate), '%Y-%m-%d') AS rdate,  -- 날짜를 'YYYY-MM-DD' 형식으로 변환
    SUM(s.pv_output) / 1000 AS total_energy_kW,  -- pv_output 값을 킬로와트로 변환
    f.lora_id,  -- RTU_facility 테이블의 lora_id 필드
    f.cid,      -- RTU_facility 테이블의 cid 필드
    u.user_id   -- RTU_user 테이블의 user_id 필드
FROM RTU_SolarInputData s
JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id  -- RTU_SolarInputData와 RTU_facility 조인
JOIN RTU_user u ON f.user_id = u.user_id  -- RTU_facility와 RTU_user 조인
WHERE u.user_id = 'BN241017-0001'  -- 특정 user_id 필터링 (BN241017-0001 사용자의 모든 LoRa ID)
AND s.fault_status = 0  -- 발전 정상
AND s.pv_output > 0    -- 발전량이 0보다 큰 경우만
AND s.energy_type IN ('0101', '0102')  -- 태양광 (단상, 삼상) 필터링
AND DATE(s.rdate) = CURDATE()  -- 오늘 날짜
GROUP BY f.lora_id, f.cid, u.user_id;  -- 그룹화 기준 필드들


* 단위 : lora_id 2라인
SELECT 
    DATE_FORMAT(MAX(s.rdate), '%Y-%m-%d') AS rdate,  -- 날짜를 'YYYY-MM-DD' 형식으로 변환
    SUM(s.pv_output) / 1000 AS total_energy_kW,  -- pv_output 값을 킬로와트로 변환
    f.lora_id,  -- RTU_facility 테이블의 lora_id 필드
    u.user_id   -- RTU_user 테이블의 user_id 필드
FROM RTU_SolarInputData s
JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id  -- RTU_SolarInputData와 RTU_facility 조인
JOIN RTU_user u ON f.user_id = u.user_id  -- RTU_facility와 RTU_user 조인
WHERE u.user_id = 'BN241017-0001'  -- 특정 user_id 필터링 (BN241017-0001 사용자의 모든 LoRa ID)
AND s.fault_status = 0  -- 발전 정상
AND s.pv_output > 0    -- 발전량이 0보다 큰 경우만
AND s.energy_type IN ('0101', '0102')  -- 태양광 (단상, 삼상) 필터링
AND DATE(s.rdate) = CURDATE()  -- 오늘 날짜
GROUP BY f.lora_id, u.user_id;  -- 그룹화 기준 필드들


* 단위 : 유저ID 1라인 (LoRa ID, CID 전체합산) 
SELECT 
    DATE_FORMAT(MAX(s.rdate), '%Y-%m-%d') AS rdate,  -- 날짜를 'YYYY-MM-DD' 형식으로 변환
    SUM(s.pv_output) / 1000 AS total_energy_kW  -- 전체 pv_output 값을 킬로와트로 변환하여 합산
FROM RTU_SolarInputData s
JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id  -- RTU_SolarInputData와 RTU_facility 조인
JOIN RTU_user u ON f.user_id = u.user_id  -- RTU_facility와 RTU_user 조인
WHERE u.user_id = 'BN241017-0001'  -- 특정 user_id 필터링 (BN241017-0001 사용자의 모든 LoRa ID)
AND s.fault_status = 0  -- 발전 정상
AND s.pv_output > 0    -- 발전량이 0보다 큰 경우만
AND s.energy_type IN ('0101', '0102')  -- 태양광 (단상, 삼상) 필터링
AND DATE(s.rdate) = CURDATE();  -- 오늘 날짜


< 오늘 발전시간 >
SELECT 
    TIMESTAMPDIFF(MINUTE, MIN(s.rdate), MAX(s.rdate)) AS generation_minutes  -- 시작과 종료 시간의 차이를 분 단위로 계산
FROM RTU_SolarInputData s
JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id  -- RTU_SolarInputData와 RTU_facility 조인
JOIN RTU_user u ON f.user_id = u.user_id  -- RTU_facility와 RTU_user 조인
WHERE u.user_id = 'BN241017-0001'  -- 특정 user_id 필터링 (BN241017-0001 사용자의 모든 LoRa ID)
AND s.energy_type IN ('0101', '0102')  -- 태양광 (단상, 삼상) 필터링
AND s.pv_output > 0  -- 발전량이 0보다 큰 경우만
AND DATE(s.rdate) = CURDATE();  -- 오늘 날짜 필터링


< 오늘 발전량과 발전시간 합산 쿼리 >
SELECT 
    DATE_FORMAT(MAX(s.rdate), '%Y-%m-%d') AS rdate,  -- 날짜를 'YYYY-MM-DD' 형식으로 변환
    SUM(s.pv_output) / 1000 AS total_energy_kW,  -- 전체 pv_output 값을 킬로와트로 변환하여 합산
    TIMESTAMPDIFF(MINUTE, MIN(s.rdate), MAX(s.rdate)) AS generation_minutes  -- 시작과 종료 시간의 차이를 분 단위로 계산
FROM RTU_SolarInputData s
JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id  -- RTU_SolarInputData와 RTU_facility 조인
JOIN RTU_user u ON f.user_id = u.user_id  -- RTU_facility와 RTU_user 조인
WHERE u.user_id = 'BN241017-0001'  -- 특정 user_id 필터링 (BN241017-0001 사용자의 모든 LoRa ID)
AND s.energy_type IN ('0101', '0102')  -- 태양광 (단상, 삼상) 필터링
AND s.pv_output > 0  -- 발전량이 0보다 큰 경우만
AND s.fault_status = 0  -- 발전 정상
AND DATE(s.rdate) = CURDATE();  -- 오늘 날짜 필터링




< 어제 발전량 >
* 단위 : f.cid단위로 출력.  7라인 
SELECT 
    DATE_FORMAT(MAX(s.rdate), '%Y-%m-%d') AS rdate,  -- 날짜를 'YYYY-MM-DD' 형식으로 변환
    SUM(s.pv_output) / 1000 AS total_energy_kW,  -- pv_output 값을 킬로와트로 변환
    f.lora_id,  -- RTU_facility 테이블의 lora_id 필드
    f.cid,      -- RTU_facility 테이블의 cid 필드
    u.user_id   -- RTU_user 테이블의 user_id 필드
FROM RTU_SolarInputData s
JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id  -- RTU_SolarInputData와 RTU_facility 조인
JOIN RTU_user u ON f.user_id = u.user_id  -- RTU_facility와 RTU_user 조인
WHERE u.user_id = 'BN241017-0001'  -- 특정 user_id 필터링 (BN241017-0001 사용자의 모든 LoRa ID)
AND s.fault_status = 0  -- 발전 정상
AND s.pv_output > 0    -- 발전량이 0보다 큰 경우만
AND s.energy_type IN ('0101', '0102')  -- 태양광 (단상, 삼상) 필터링
AND DATE(s.rdate) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)  -- 어제 날짜
GROUP BY f.lora_id, f.cid, u.user_id;  -- 그룹화 기준 필드들


* 단위 : lora_id 2라인
SELECT 
    DATE_FORMAT(MAX(s.rdate), '%Y-%m-%d') AS rdate,  -- 날짜를 'YYYY-MM-DD' 형식으로 변환
    SUM(s.pv_output) / 1000 AS total_energy_kW,  -- pv_output 값을 킬로와트로 변환
    f.lora_id,  -- RTU_facility 테이블의 lora_id 필드
    u.user_id   -- RTU_user 테이블의 user_id 필드
FROM RTU_SolarInputData s
JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id  -- RTU_SolarInputData와 RTU_facility 조인
JOIN RTU_user u ON f.user_id = u.user_id  -- RTU_facility와 RTU_user 조인
WHERE u.user_id = 'BN241017-0001'  -- 특정 user_id 필터링 (BN241017-0001 사용자의 모든 LoRa ID)
AND s.fault_status = 0  -- 발전 정상
AND s.pv_output > 0    -- 발전량이 0보다 큰 경우만
AND s.energy_type IN ('0101', '0102')  -- 태양광 (단상, 삼상) 필터링
AND DATE(s.rdate) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)  -- 어제 날짜
GROUP BY f.lora_id, u.user_id;  -- 그룹화 기준 필드들


* 단위 : 유저ID 1라인 (LoRa ID, CID 전체합산) 
SELECT 
    DATE_FORMAT(MAX(s.rdate), '%Y-%m-%d') AS rdate,  -- 날짜를 'YYYY-MM-DD' 형식으로 변환
    SUM(s.pv_output) / 1000 AS total_energy_kW  -- 전체 pv_output 값을 킬로와트로 변환하여 합산
FROM RTU_SolarInputData s
JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id  -- RTU_SolarInputData와 RTU_facility 조인
JOIN RTU_user u ON f.user_id = u.user_id  -- RTU_facility와 RTU_user 조인
WHERE u.user_id = 'BN241017-0001'  -- 특정 user_id 필터링 (BN241017-0001 사용자의 모든 LoRa ID)
AND s.fault_status = 0  -- 발전 정상
AND s.pv_output > 0    -- 발전량이 0보다 큰 경우만
AND s.energy_type IN ('0101', '0102')  -- 태양광 (단상, 삼상) 필터링
AND s.fault_status = 0  -- 발전 정상
AND s.pv_output > 0    -- 발전량이 0보다 큰 경우만
AND DATE(s.rdate) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)  -- 어제 날짜

 

< 특정 기간의 발전량 (여러줄) / 로라별 한레코드 (cid는 합친거) >
SELECT 
    DATE_FORMAT(s.rdate, '%Y-%m-%d') AS rdate,  -- 날짜를 'YYYY-MM-DD' 형식으로 변환
    HOUR(s.rdate) AS hour,  -- rdate의 시간 정보만 추출하여 새로운 컬럼으로 표시 (0시, 1시 등)
    SUM(s.pv_output) / 1000 AS total_energy_kW,  -- pv_output 값을 킬로와트로 변환
    GROUP_CONCAT(DISTINCT f.lora_id) AS lora_ids,  -- 같은 시간대의 LoRa ID를 하나로 결합
    GROUP_CONCAT(DISTINCT f.cid) AS cids,  -- 같은 시간대의 CID를 하나로 결합
    u.user_id   -- RTU_user 테이블의 user_id 필드
FROM RTU_SolarInputData s
JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id  -- RTU_SolarInputData와 RTU_facility 조인
JOIN RTU_user u ON f.user_id = u.user_id  -- RTU_facility와 RTU_user 조인
WHERE u.user_id = 'BN241017-0001'  -- 특정 user_id 필터링 (BN241017-0001 사용자의 모든 LoRa ID)
 
AND s.energy_type IN ('0101', '0102')  -- 태양광 (단상, 삼상) 필터링
AND DATE(s.rdate) BETWEEN '2024-10-01' AND '2024-10-31'  -- 10월 한 달 동안의 발전량 필터링
GROUP BY rdate, hour, u.user_id  -- 날짜, 시간, 사용자 ID로 그룹화
ORDER BY rdate, hour;  -- 날짜와 시간 순으로 정렬


< 특정 기간의 발전량 (여러줄) / cid당 한레코드 >
SELECT 
    DATE_FORMAT(s.rdate, '%Y-%m-%d') AS rdate,  -- 날짜를 'YYYY-MM-DD' 형식으로 변환
    HOUR(s.rdate) AS hour,  -- rdate의 시간 정보만 추출하여 새로운 컬럼으로 표시 (0시, 1시 등)
    SUM(s.pv_output) / 1000 AS total_energy_kW,  -- pv_output 값을 킬로와트로 변환
    f.lora_id,  -- RTU_facility 테이블의 lora_id 필드
    f.cid,      -- RTU_facility 테이블의 cid 필드 (개별 레코드로 표시)
    u.user_id   -- RTU_user 테이블의 user_id 필드
FROM RTU_SolarInputData s
JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id  -- RTU_SolarInputData와 RTU_facility 조인
JOIN RTU_user u ON f.user_id = u.user_id  -- RTU_facility와 RTU_user 조인
WHERE u.user_id = 'BN241017-0001'  -- 특정 user_id 필터링 (BN241017-0001 사용자의 모든 LoRa ID)
AND s.energy_type IN ('0101', '0102')  -- 태양광 (단상, 삼상) 필터링
AND DATE(s.rdate) BETWEEN '2024-10-01' AND '2024-10-31'  -- 10월 한 달 동안의 발전량 필터링
GROUP BY rdate, hour, f.lora_id, f.cid, u.user_id  -- 날짜, 시간, LoRa ID, CID, 사용자 ID로 그룹화
ORDER BY rdate, hour;  -- 날짜와 시간 순으로 정렬




< 특정 기간의 발전량 (날짜와 시간별) >
SELECT 
    DATE_FORMAT(MIN(s.rdate), '%Y-%m-%d') AS rdate,  -- MIN을 사용하여 그룹별 가장 빠른 날짜로 변환
    HOUR(s.rdate) AS hour,  -- rdate의 시간 정보만 추출하여 새로운 컬럼으로 표시 (0시, 1시 등)
    SUM(s.pv_output) / 1000 AS total_energy_kW,  -- 같은 시간대의 pv_output 값을 킬로와트로 변환하여 합산
    GROUP_CONCAT(DISTINCT f.lora_id) AS lora_ids,  -- 같은 시간대의 LoRa ID를 하나로 결합
    u.user_id   -- RTU_user 테이블의 user_id 필드
FROM RTU_SolarInputData s
JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id  -- RTU_SolarInputData와 RTU_facility 조인
JOIN RTU_user u ON f.user_id = u.user_id  -- RTU_facility와 RTU_user 조인
WHERE u.user_id = 'BN241017-0001'  -- 특정 user_id 필터링 (BN241017-0001 사용자의 모든 LoRa ID)
AND s.energy_type IN ('0101', '0102')  -- 태양광 (단상, 삼상) 필터링
AND DATE(s.rdate) BETWEEN '2024-10-01' AND '2024-10-31'  -- 특정 기간 동안의 발전량 필터링
GROUP BY DATE(s.rdate), HOUR(s.rdate), u.user_id  -- rdate의 날짜 정보와 시간, 사용자 ID로 그룹화
ORDER BY rdate, hour  -- 날짜와 시간 순으로 정렬
LIMIT 0, 100000;


< 특정 기간의 발전량 (오늘날짜와 시간별) >
SELECT 
    DATE_FORMAT(MIN(s.rdate), '%Y-%m-%d') AS rdate,  -- MIN을 사용하여 그룹별 가장 빠른 날짜로 변환
    HOUR(s.rdate) AS hour,  -- rdate의 시간 정보만 추출하여 새로운 컬럼으로 표시 (0시, 1시 등)
    SUM(s.pv_output) / 1000 AS total_energy_kW,  -- 같은 시간대의 pv_output 값을 킬로와트로 변환하여 합산
    GROUP_CONCAT(DISTINCT f.lora_id) AS lora_ids,  -- 같은 시간대의 LoRa ID를 하나로 결합
    u.user_id   -- RTU_user 테이블의 user_id 필드
FROM RTU_SolarInputData s
JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id  -- RTU_SolarInputData와 RTU_facility 조인
JOIN RTU_user u ON f.user_id = u.user_id  -- RTU_facility와 RTU_user 조인
WHERE u.user_id = 'BN241017-0001'  -- 특정 user_id 필터링 (BN241017-0001 사용자의 모든 LoRa ID)
AND s.energy_type IN ('0101', '0102')  -- 태양광 (단상, 삼상) 필터링
AND DATE(s.rdate) = CURDATE()  -- 특정 기간 동안의 발전량 필터링
GROUP BY DATE(s.rdate), HOUR(s.rdate), u.user_id  -- rdate의 날짜 정보와 시간, 사용자 ID로 그룹화
ORDER BY rdate, hour  -- 날짜와 시간 순으로 정렬
LIMIT 0, 25;


< 특정 기간의 발전량 (어제날짜와 시간별) >
SELECT 
    DATE_FORMAT(MIN(s.rdate), '%Y-%m-%d') AS rdate,  -- MIN을 사용하여 그룹별 가장 빠른 날짜로 변환
    HOUR(s.rdate) AS hour,  -- rdate의 시간 정보만 추출하여 새로운 컬럼으로 표시 (0시, 1시 등)
    SUM(s.pv_output) / 1000 AS total_energy_kW,  -- 같은 시간대의 pv_output 값을 킬로와트로 변환하여 합산
    GROUP_CONCAT(DISTINCT f.lora_id) AS lora_ids,  -- 같은 시간대의 LoRa ID를 하나로 결합
    u.user_id   -- RTU_user 테이블의 user_id 필드
FROM RTU_SolarInputData s
JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id  -- RTU_SolarInputData와 RTU_facility 조인
JOIN RTU_user u ON f.user_id = u.user_id  -- RTU_facility와 RTU_user 조인
WHERE u.user_id = 'BN241017-0001'  -- 특정 user_id 필터링 (BN241017-0001 사용자의 모든 LoRa ID)
AND s.energy_type IN ('0101', '0102')  -- 태양광 (단상, 삼상) 필터링
AND DATE(s.rdate) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)  -- 어제 날짜의 발전량 필터링
GROUP BY DATE(s.rdate), HOUR(s.rdate), u.user_id  -- rdate의 날짜 정보와 시간, 사용자 ID로 그룹화
ORDER BY rdate, hour  -- 날짜와 시간 순으로 정렬
LIMIT 0, 24;




















★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★


RTU_SolarInputData 테이블의 ltid와 RTU_facility 테이블의 lora_id 필드를 조인하고,
RTU_user 테이블의 user_id와 RTU_facility 테이블의 user_id를 조인할 것, 
RTU_SolarInputData테이블의 필요한 필드들을 출력하며, 
또한 RTU_facility 테이블의 lora_id 필드,  cid 필드,  RTU_user 테이블의 user_id를 같이 출력할 것
----------------------------------------------------------------------------------------------------------------------------

< 오늘 발전시간 >

* 단위 : lora_id 2라인
SELECT 
    DATE_FORMAT(MAX(s.rdate), '%Y-%m-%d') AS rdate,  -- 날짜를 'YYYY-MM-DD' 형식으로 변환
    SUM(s.pv_output) / 1000 AS total_energy_kW,  -- pv_output 값을 킬로와트로 변환
    f.lora_id,  -- RTU_facility 테이블의 lora_id 필드
    u.user_id,  -- RTU_user 테이블의 user_id 필드
    COUNT(DISTINCT HOUR(s.rdate)) AS generation_hours  -- 발전이 일어난 시간(시간별 고유 값) 카운트
FROM RTU_SolarInputData s
JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id  -- RTU_SolarInputData와 RTU_facility 조인
JOIN RTU_user u ON f.user_id = u.user_id  -- RTU_facility와 RTU_user 조인
WHERE u.user_id = 'BN241017-0001'  -- 특정 user_id 필터링
AND s.energy_type IN ('0101', '0102')  -- 태양광 (단상, 삼상) 필터링
AND s.pv_output > 0  -- 발전량이 0보다 큰 경우만
AND DATE(s.rdate) = CURDATE()  -- 오늘 날짜 필터링
GROUP BY f.lora_id, u.user_id;  -- 각 LoRa ID 별로 그룹화

* 단위 : cid 7라인
SELECT 
    DATE_FORMAT(MAX(s.rdate), '%Y-%m-%d') AS rdate,  -- 날짜를 'YYYY-MM-DD' 형식으로 변환
    SUM(s.pv_output) / 1000 AS total_energy_kW,  -- pv_output 값을 킬로와트로 변환
    f.lora_id,  -- RTU_facility 테이블의 lora_id 필드
    f.cid,  -- RTU_facility 테이블의 cid 필드
    u.user_id,  -- RTU_user 테이블의 user_id 필드
    COUNT(DISTINCT HOUR(s.rdate)) AS generation_hours  -- 발전이 일어난 시간(시간별 고유 값) 카운트
FROM RTU_SolarInputData s
JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id  -- RTU_SolarInputData와 RTU_facility 조인
JOIN RTU_user u ON f.user_id = u.user_id  -- RTU_facility와 RTU_user 조인
WHERE u.user_id = 'BN241017-0001'  -- 특정 user_id 필터링
AND s.energy_type IN ('0101', '0102')  -- 태양광 (단상, 삼상) 필터링
AND s.pv_output > 0  -- 발전량이 0보다 큰 경우만
AND DATE(s.rdate) = CURDATE()  -- 오늘 날짜 필터링
GROUP BY f.lora_id, f.cid, u.user_id  -- 각 LoRa ID 및 cid 별로 그룹화
ORDER BY f.cid, f.lora_id;  -- cid와 lora_id 순으로 정렬

* 단위 : 유저ID
SELECT 
    MAX(subquery.rdate) AS rdate,  -- 서브쿼리에서 넘긴 rdate의 최대값을 사용
    SUM(subquery.total_energy_kW) AS total_energy_kW,  -- 킬로와트 변환 후 합산
    GROUP_CONCAT(DISTINCT subquery.lora_id) AS lora_ids,  -- lora_id 결합
    subquery.user_id,  -- 사용자 ID
    SUM(subquery.generation_hours_per_cid) AS total_generation_hours  -- 각 cid별 발전 시간 합산
FROM (
    SELECT 
        MAX(DATE_FORMAT(s.rdate, '%Y-%m-%d')) AS rdate,  -- 날짜를 'YYYY-MM-DD' 형식으로 변환 후 최대값
        f.lora_id,  -- LoRa ID
        f.cid,  -- cid
        u.user_id,  -- 사용자 ID
        SUM(s.pv_output) / 1000 AS total_energy_kW,  -- 킬로와트로 변환하여 합산
        COUNT(DISTINCT HOUR(s.rdate)) AS generation_hours_per_cid  -- 고유한 시간대 카운트
    FROM RTU_SolarInputData s
    JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id  -- RTU_SolarInputData와 RTU_facility 조인
    JOIN RTU_user u ON f.user_id = u.user_id  -- RTU_facility와 RTU_user 조인
    WHERE u.user_id = 'BN241017-0001'  -- 특정 사용자 필터링
    AND s.energy_type IN ('0101', '0102')  -- 태양광 (단상, 삼상) 필터링
    AND s.pv_output > 0  -- 발전량이 0보다 큰 경우만
    AND DATE(s.rdate) = CURDATE()  -- 오늘 날짜 필터링
    GROUP BY f.cid, f.lora_id, u.user_id  -- cid, LoRa ID, 사용자 ID별로 그룹화
) AS subquery  
GROUP BY subquery.user_id  -- 상위 쿼리에서 사용자 ID별로 그룹화
ORDER BY MAX(subquery.rdate)  -- 최대 rdate 기준으로 정렬
LIMIT 0, 25;


< 어제 발전시간 >

* 단위 : lora_id 2라인
SELECT 
    DATE_FORMAT(MAX(s.rdate), '%Y-%m-%d') AS rdate,  -- 날짜를 'YYYY-MM-DD' 형식으로 변환
    SUM(s.pv_output) / 1000 AS total_energy_kW,  -- pv_output 값을 킬로와트로 변환
    f.lora_id,  -- RTU_facility 테이블의 lora_id 필드
    u.user_id,  -- RTU_user 테이블의 user_id 필드
    COUNT(DISTINCT HOUR(s.rdate)) AS generation_hours  -- 발전이 일어난 시간(시간별 고유 값) 카운트
FROM RTU_SolarInputData s
JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id  -- RTU_SolarInputData와 RTU_facility 조인
JOIN RTU_user u ON f.user_id = u.user_id  -- RTU_facility와 RTU_user 조인
WHERE u.user_id = 'BN241017-0001'  -- 특정 user_id 필터링
AND s.fault_status = 0  -- 발전 정상
AND s.pv_output > 0    -- 발전량이 0보다 큰 경우만	
AND s.energy_type IN ('0101', '0102')  -- 태양광 (단상, 삼상) 필터링
AND s.pv_output > 0  -- 발전량이 0보다 큰 경우만
AND DATE(s.rdate) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)  -- 어제 날짜 필터링
GROUP BY f.lora_id, u.user_id;  -- 각 LoRa ID 별로 그룹화

* 단위 : cid 7라인
SELECT 
    DATE_FORMAT(MAX(s.rdate), '%Y-%m-%d') AS rdate,  -- 날짜를 'YYYY-MM-DD' 형식으로 변환
    SUM(s.pv_output) / 1000 AS total_energy_kW,  -- pv_output 값을 킬로와트로 변환
    f.lora_id,  -- RTU_facility 테이블의 lora_id 필드
    f.cid,  -- RTU_facility 테이블의 cid 필드
    u.user_id,  -- RTU_user 테이블의 user_id 필드
    COUNT(DISTINCT HOUR(s.rdate)) AS generation_hours  -- 발전이 일어난 시간(시간별 고유 값) 카운트
FROM RTU_SolarInputData s
JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id  -- RTU_SolarInputData와 RTU_facility 조인
JOIN RTU_user u ON f.user_id = u.user_id  -- RTU_facility와 RTU_user 조인
WHERE u.user_id = 'BN241017-0001'  -- 특정 user_id 필터링
AND s.fault_status = 0  -- 발전 정상
AND s.pv_output > 0    -- 발전량이 0보다 큰 경우만	
AND s.energy_type IN ('0101', '0102')  -- 태양광 (단상, 삼상) 필터링
AND s.pv_output > 0  -- 발전량이 0보다 큰 경우만
AND DATE(s.rdate) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)  -- 어제 날짜 필터링
GROUP BY f.lora_id, f.cid, u.user_id  -- 각 LoRa ID 및 cid 별로 그룹화
ORDER BY f.cid, f.lora_id;  -- cid와 lora_id 순으로 정렬

* 단위 : 유저ID
SELECT 
    MAX(subquery.rdate) AS rdate,  -- 서브쿼리에서 넘긴 rdate의 최대값을 사용
    SUM(subquery.total_energy_kW) AS total_energy_kW,  -- 킬로와트 변환 후 합산
    GROUP_CONCAT(DISTINCT subquery.lora_id) AS lora_ids,  -- lora_id 결합
    subquery.user_id,  -- 사용자 ID
    SUM(subquery.generation_hours_per_cid) AS total_generation_hours  -- 각 cid별 발전 시간 합산
FROM (
    SELECT 
        MAX(DATE_FORMAT(s.rdate, '%Y-%m-%d')) AS rdate,  -- 날짜를 'YYYY-MM-DD' 형식으로 변환 후 최대값
        f.lora_id,  -- LoRa ID
        f.cid,  -- cid
        u.user_id,  -- 사용자 ID
        SUM(s.pv_output) / 1000 AS total_energy_kW,  -- 킬로와트로 변환하여 합산
        COUNT(DISTINCT HOUR(s.rdate)) AS generation_hours_per_cid  -- 고유한 시간대 카운트
    FROM RTU_SolarInputData s
    JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id  -- RTU_SolarInputData와 RTU_facility 조인
    JOIN RTU_user u ON f.user_id = u.user_id  -- RTU_facility와 RTU_user 조인
    WHERE u.user_id = 'BN241017-0001'  -- 특정 사용자 필터링
	AND s.fault_status = 0  -- 발전 정상
	AND s.pv_output > 0    -- 발전량이 0보다 큰 경우만	
    AND s.energy_type IN ('0101', '0102')  -- 태양광 (단상, 삼상) 필터링
    AND s.pv_output > 0  -- 발전량이 0보다 큰 경우만
    AND DATE(s.rdate) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)  -- 어제 날짜 필터링
    GROUP BY f.cid, f.lora_id, u.user_id  -- cid, LoRa ID, 사용자 ID별로 그룹화
) AS subquery  
GROUP BY subquery.user_id  -- 상위 쿼리에서 사용자 ID별로 그룹화
ORDER BY MAX(subquery.rdate)  -- 최대 rdate 기준으로 정렬
LIMIT 0, 25;


 
< 오늘하루 발전량 예상 >
SELECT    
    CURDATE() AS today,  
    (yesterday_total_energy_kW +   
    ((today_sunlight_minutes - yesterday_sunlight_minutes) / yesterday_sunlight_minutes) * yesterday_total_energy_kW) AS estimated_today_energy_kW    
FROM (  
          
    SELECT    
        DATE_FORMAT(MAX(s.rdate), '%Y-%m-%d') AS yesterday,    
        SUM(s.pv_output) / 1000 AS yesterday_total_energy_kW,   
        TIMESTAMPDIFF(MINUTE, MIN(s.rdate), MAX(s.rdate)) AS generation_minutes     
    FROM RTU_SolarInputData s    
    JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id     
    JOIN RTU_user u ON f.user_id = u.user_id     
    WHERE u.user_id = 'BN241017-0001'     
    AND s.energy_type IN ('0101', '0102')    
    AND s.fault_status = 0     
    AND s.pv_output > 0      
    AND DATE(s.rdate) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)  
) AS t1,  
(  
    SELECT  
        TIMESTAMPDIFF(MINUTE, st_today.sunrise, st_today.sunset) AS today_sunlight_minutes,  
        TIMESTAMPDIFF(MINUTE, st_yesterday.sunrise, st_yesterday.sunset) AS yesterday_sunlight_minutes  
    FROM RTU_sun_times_365 st_today  
    JOIN RTU_sun_times_365 st_yesterday  
        ON DATE(st_yesterday.rdate) = DATE_SUB(st_today.rdate, INTERVAL 1 DAY)  
    WHERE DATE(st_today.rdate) = CURDATE()  
) AS t2;  


 
< 오늘하루 발전시간 예상 >
SELECT  
     CURDATE() AS today,   
    (yesterday_generation_minutes +  
    ((today_sunlight_minutes - yesterday_sunlight_minutes) / yesterday_sunlight_minutes) * yesterday_generation_minutes) AS estimated_today_generation_minutes  
FROM ( 
 			
    SELECT   
        TIMESTAMPDIFF(MINUTE, MIN(s.rdate), MAX(s.rdate)) AS yesterday_generation_minutes 
    FROM RTU_SolarInputData s   
    JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id    
    JOIN RTU_user u ON f.user_id = u.user_id    
    WHERE u.user_id = 'BN241017-0001'     
    AND s.energy_type IN ('0101', '0102')   
    AND s.fault_status = 0    
    AND s.pv_output > 0     
    AND DATE(s.rdate) = DATE_SUB(CURDATE(), INTERVAL 1 DAY) 
) AS t1, 
( 
			
    SELECT 
        TIMESTAMPDIFF(MINUTE, st_today.sunrise, st_today.sunset) AS today_sunlight_minutes, 
        TIMESTAMPDIFF(MINUTE, st_yesterday.sunrise, st_yesterday.sunset) AS yesterday_sunlight_minutes 
    FROM RTU_sun_times_365 st_today 
    JOIN RTU_sun_times_365 st_yesterday 
        ON DATE(st_yesterday.rdate) = DATE_SUB(st_today.rdate, INTERVAL 1 DAY) 
    WHERE DATE(st_today.rdate) = CURDATE() 
) AS t2; 
 