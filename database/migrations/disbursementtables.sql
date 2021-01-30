-- disbursement.tb_disbursement definition

CREATE TABLE `tb_disbursement` (
  `disbursement_id` varchar(36) NOT NULL,
  `seller_id` varchar(36) NOT NULL,
  `transaction_id` bigint(20) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `fee` int(11) DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `receipt` varchar(255) DEFAULT NULL,
  `time_served` varchar(50) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  `created_date` timestamp(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  `created_by` varchar(36) DEFAULT NULL,
  `updated_date` timestamp(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  `updated_by` varchar(36) DEFAULT NULL,
  PRIMARY KEY (`disbursement_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- disbursement.tb_seller definition

CREATE TABLE `tb_seller` (
  `seller_id` varchar(36) NOT NULL,
  `seller_name` varchar(255) DEFAULT NULL,
  `seller_email` varchar(255) DEFAULT NULL,
  `seller_bank_code` varchar(5) DEFAULT NULL,
  `seller_account_number` varchar(255) DEFAULT NULL,
  `seller_account_name` varchar(255) DEFAULT NULL,
  `created_date` timestamp(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  `updated_date` timestamp(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  `updated_by` varchar(36) DEFAULT NULL,
  `created_by` varchar(36) DEFAULT NULL,
  PRIMARY KEY (`seller_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- disbursement.tb_seller_deposit definition

CREATE TABLE `tb_seller_deposit` (
  `deposit_id` varchar(36) NOT NULL,
  `seller_id` varchar(36) NOT NULL,
  `amount` int(11) DEFAULT NULL,
  `created_date` timestamp(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  `created_by` varchar(36) DEFAULT NULL,
  `update_date` timestamp(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  `updated_by` varchar(36) DEFAULT NULL,
  PRIMARY KEY (`deposit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- disbursement.tb_seller_deposit_log definition

CREATE TABLE `tb_seller_deposit_log` (
  `log_id` varchar(36) NOT NULL,
  `deposit_id` varchar(36) NOT NULL,
  `log_description` varchar(255) DEFAULT NULL,
  `deposit_before` int(11) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `created_date` timestamp(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  `created_by` varchar(36) DEFAULT NULL,
  `updated_date` timestamp(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  `updated_by` varchar(36) DEFAULT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- disbursement.tb_transaction_log definition

CREATE TABLE `tb_transaction_log` (
  `log_id` varchar(36) NOT NULL,
  `transaction_id` bigint(20) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `fee` int(11) DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `receipt` varchar(255) DEFAULT NULL,
  `bank_code` varchar(5) DEFAULT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `beneficiary_name` varchar(255) DEFAULT NULL,
  `time_served` varchar(50) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  `created_date` timestamp(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  `created_by` varchar(36) DEFAULT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;