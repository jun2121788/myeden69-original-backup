CREATE TABLE  /*TABLE_PREFIX*/t_payment_pro_invoice (
  pk_i_id INT NOT NULL AUTO_INCREMENT ,
  dt_date DATETIME NOT NULL ,
  s_code VARCHAR( 255 ) NOT NULL ,
  i_amount BIGINT(20) NULL,
  s_currency_code VARCHAR( 3 ) NULL ,
  s_email VARCHAR( 200 ) NULL ,
  fk_i_user_id INT NULL ,
  s_source VARCHAR( 10 ) NOT NULL,
  i_status INT NOT NULL,

  PRIMARY KEY (pk_i_id),
  KEY source_status (s_source,i_status),
  KEY i_status (i_status)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';


CREATE TABLE  /*TABLE_PREFIX*/t_payment_pro_invoice_row (
  pk_i_id INT NOT NULL AUTO_INCREMENT ,
  fk_i_invoice_id INT NOT NULL,
  s_concept VARCHAR( 200 ) NOT NULL ,
  i_amount BIGINT(20) NULL,
  i_quantity INT NOT NULL DEFAULT 1,
  fk_i_item_id INT NULL ,
  i_product_type VARCHAR( 30 ) NOT NULL,

  PRIMARY KEY(pk_i_id),
  FOREIGN KEY (fk_i_invoice_id) REFERENCES /*TABLE_PREFIX*/t_payment_pro_invoice (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';

CREATE TABLE  /*TABLE_PREFIX*/t_payment_pro_pending_row (
  pk_i_id INT NOT NULL AUTO_INCREMENT ,
  s_code char(40) NOT NULL,
  dt_date DATETIME NOT NULL,
  s_concept VARCHAR( 200 ) NOT NULL ,
  i_amount BIGINT(20) NULL,
  i_quantity INT NOT NULL DEFAULT 1,
  fk_i_item_id INT NULL ,
  i_product_type VARCHAR( 30 ) NOT NULL,

  PRIMARY KEY(pk_i_id),
  INDEX idx_dt_date (dt_date),
  INDEX idx_s_code (s_code)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';


CREATE TABLE /*TABLE_PREFIX*/t_payment_pro_wallet (
    fk_i_user_id INT UNSIGNED NOT NULL,
    i_amount BIGINT(20) NULL,

        PRIMARY KEY (fk_i_user_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';

CREATE TABLE /*TABLE_PREFIX*/t_payment_pro_premium (
    fk_i_item_id INT UNSIGNED NOT NULL,
    dt_date DATETIME NOT NULL ,
    dt_expiration_date DATETIME NOT NULL ,
    fk_i_invoice_id INT NOT NULL,

        PRIMARY KEY (fk_i_item_id),
        FOREIGN KEY (fk_i_invoice_id) REFERENCES /*TABLE_PREFIX*/t_payment_pro_invoice (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';

CREATE TABLE /*TABLE_PREFIX*/t_payment_pro_publish (
    fk_i_item_id INT UNSIGNED NOT NULL,
    dt_date DATETIME NOT NULL ,
    b_paid BOOLEAN NOT NULL DEFAULT FALSE,
    b_enabled BOOLEAN NOT NULL DEFAULT TRUE,
    fk_i_invoice_id INT NULL,

        PRIMARY KEY (fk_i_item_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';

CREATE TABLE /*TABLE_PREFIX*/t_payment_pro_prices (
  fk_i_category_id INT UNSIGNED NOT NULL,
  i_publish_cost INT NULL ,
  i_premium_cost INT NULL ,
  i_image_cost INT NULL ,

  PRIMARY KEY (fk_i_category_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';

CREATE TABLE /*TABLE_PREFIX*/t_payment_pro_mail_queue (
  pk_i_id INT NOT NULL AUTO_INCREMENT ,
  dt_send_date DATETIME NOT NULL ,
  fk_i_item_id INT NULL ,
  b_publish BOOLEAN NOT NULL DEFAULT TRUE,
  b_premium BOOLEAN NOT NULL DEFAULT TRUE,

  PRIMARY KEY (pk_i_id),
  KEY dt_send_date (dt_send_date)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';