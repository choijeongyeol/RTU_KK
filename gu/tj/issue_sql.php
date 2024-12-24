BEGIN
    DECLARE issue_type INT;
    DECLARE userIdx INT DEFAULT NULL;
    DECLARE loraIdx INT DEFAULT NULL;
    DECLARE concopy VARCHAR(255);  
    DECLARE viewline INT DEFAULT 1;
    DECLARE con_count INT DEFAULT 0;
    DECLARE existing_issue_count INT DEFAULT 0;
    
    SET concopy = NEW.con;    

    -- 장애 판단 로직을 설정합니다.
    IF NEW.pv_voltage = 1000 THEN
        SET issue_type = 1;  -- 장애 유형 1 설정
    ELSEIF SUBSTRING(NEW.con, 9, 2) = '39' THEN
        -- 최근 20분 이내의 동일 cid와 con 값의 조건에 맞는 데이터 수를 확인
        SELECT COUNT(*) INTO con_count
        FROM RTU_SolarInputData
        WHERE cid = NEW.cid
          AND SUBSTRING(con, 9, 2) <> '39'
         AND rdate >= NOW() - INTERVAL 1 HOUR;
        
        -- 조건에 따라 viewline 설정
        IF con_count = 0 THEN
            SET viewline = 1;
        ELSE
            SET viewline = 0;
        END IF;  

        SET issue_type = 2;  -- 장애 유형 2 설정
    ELSEIF NEW.system_voltage_r > 500 THEN
        SET issue_type = 3;  -- 장애 유형 3 설정
    ELSE
        SET issue_type = NULL;  -- 장애가 아닌 경우
    END IF;

    -- 장애로 판단되었을 때만 RTU_Issue_History_New 테이블에 데이터를 삽입합니다.
    IF issue_type IS NOT NULL THEN
        -- 동일한 CID와 장애 유형(issue_type)이 이미 존재하는지 확인
        SELECT COUNT(*) INTO existing_issue_count
        FROM RTU_Issue_History_New
        WHERE facility_id = NEW.cid
          AND issue_name = issue_type
          AND (viewline = 1 OR NOT EXISTS (
              SELECT 1 FROM RTU_Issue_History_New
              WHERE facility_id = NEW.cid AND issue_name = issue_type AND viewline = 1
          ));
          
        -- 동일 장애가 존재하지 않거나, viewline=1인 항목이 없을 경우에만 삽입
        IF existing_issue_count = 0 THEN
            -- RTU_facility 테이블을 통해 user_idx 값을 구합니다.
            SELECT u.user_idx INTO userIdx
            FROM RTU_facility f
            JOIN RTU_user u ON f.user_id = u.user_id
            WHERE f.cid = NEW.cid
            LIMIT 1;

            -- RTU_lora 테이블을 통해 lora_idx 값을 구합니다.
            SELECT l.id INTO loraIdx
            FROM RTU_facility f
            JOIN RTU_lora l ON f.lora_id = l.lora_id
            WHERE f.cid = NEW.cid
            LIMIT 1;

            -- 장애 기록을 RTU_Issue_History_New 테이블에 삽입합니다.
            INSERT INTO RTU_Issue_History_New (issue_name, issue_date, facility_id, user_idx, lora_idx, status, con, viewline)
            VALUES (
                issue_type,                            -- 장애 유형
                NEW.rdate,                             -- 장애 발생일시 (RTU_SolarInputData의 rdate 필드를 사용)
                NEW.cid,                               -- facility_id (RTU_SolarInputData의 cid 필드)
                userIdx,                               -- user_idx (위에서 구한 값)
                loraIdx,                               -- lora_idx (위에서 구한 값)
                '0',                                   -- status (기본 상태: '미신청')
                concopy,                               -- con (NEW.con 값)
                viewline                               -- viewline 값
            );
        END IF;
    END IF;

END


------------------------------------