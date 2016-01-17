<?php

    class ModelPaymentPro extends DAO
    {

        private static $instance ;

        public static function newInstance()
        {
            if( !self::$instance instanceof self ) {
                self::$instance = new self ;
            }
            return self::$instance ;
        }

        function __construct()
        {
            parent::__construct();
        }

        public function getTable_invoice()
        {
            return DB_TABLE_PREFIX.'t_payment_pro_invoice';
        }

        public function getTable_invoice_row()
        {
            return DB_TABLE_PREFIX.'t_payment_pro_invoice_row';
        }

        public function getTable_pending_row()
        {
            return DB_TABLE_PREFIX.'t_payment_pro_pending_row';
        }

        public function getTable_wallet()
        {
            return DB_TABLE_PREFIX.'t_payment_pro_wallet';
        }

        public function getTable_premium()
        {
            return DB_TABLE_PREFIX.'t_payment_pro_premium';
        }

        public function getTable_publish()
        {
            return DB_TABLE_PREFIX.'t_payment_pro_publish';
        }

        public function getTable_prices()
        {
            return DB_TABLE_PREFIX.'t_payment_pro_prices';
        }

        public function getTable_mail_queue()
        {
            return DB_TABLE_PREFIX.'t_payment_pro_mail_queue';
        }

        public function import($file)
        {
            $sql = file_get_contents($file);

            if(! $this->dao->importSQL($sql) ){
                throw new Exception( "Error importSQL::ModelPaymentPro<br>".$file ) ;
            }
        }

        public function install() {

            $confPath = PAYMENT_PRO_PATH . 'payments/';
            $dir = opendir($confPath);
            while($file = readdir($dir)) {
                if(is_dir($confPath . $file) && $file!='.' && $file!='..') {
                    if(file_exists($confPath . $file . '/load.php')) {
                        include_once $confPath . $file . '/load.php';
                    }
                }
            }
            closedir($dir);
            unset($dir);
            $this->import(PAYMENT_PRO_PATH . 'struct.sql');

            osc_set_preference('version', '200', 'payment_pro', 'INTEGER');
            osc_set_preference('default_premium_cost', '1.0', 'payment_pro', 'STRING');
            osc_set_preference('allow_premium', '0', 'payment_pro', 'BOOLEAN');
            osc_set_preference('default_publish_cost', '1.0', 'payment_pro', 'STRING');
            osc_set_preference('pay_per_post', '0', 'payment_pro', 'BOOLEAN');
            osc_set_preference('premium_days', '7', 'payment_pro', 'INTEGER');
            osc_set_preference('currency', 'USD', 'payment_pro', 'STRING');
            osc_set_preference('pack_price_1', '', 'payment_pro', 'STRING');
            osc_set_preference('pack_price_2', '', 'payment_pro', 'STRING');
            osc_set_preference('pack_price_3', '', 'payment_pro', 'STRING');
            osc_set_preference('last_purge', time(), 'payment_pro', 'INTEGER');

            osc_run_hook('payment_pro_install');

            $limit = 20000;
            $this->dao->select('COUNT(*) as total') ;
            $this->dao->from(DB_TABLE_PREFIX.'t_item') ;
            $result = $this->dao->get();
            $total = $result->row();
            $total = (int)$total['total'];
            $steps = ceil($total/$limit);
            for($step=0;$step<$steps;$step++) {

                $this->dao->select('pk_i_id, b_enabled');
                $this->dao->from(DB_TABLE_PREFIX . 't_item');
                $this->dao->orderBy('pk_i_id', 'ASC');
                $this->dao->limit($limit, $limit*$step);
                $result = $this->dao->get();
                $query = 'INSERT INTO ' . $this->getTable_publish() . ' (fk_i_item_id, b_paid, b_enabled, dt_date) VALUES ';
                if ($result) {
                    $items = $result->result();
                    $date = date("Y-m-d H:i:s");
                    $values = array();
                    $k = 0;
                    foreach ($items as $key => $item) {
                        $values[] = '(' . $item['pk_i_id'] . ', 1, ' . $item['b_enabled'] . ', "' . $date . '")';
                        $k++;
                        if ($k >= 500) {
                            $this->dao->query($query . implode(",", $values) . ";");
                            $k = 0;
                            $values = array();
                        }
                        unset($items[$key]);
                    }
                    $this->dao->query($query . implode(",", $values) . ";");
                }

            }

            $description[osc_language()]['s_title'] = '{WEB_TITLE} - Publish option for your ad: {ITEM_TITLE}';
            $description[osc_language()]['s_text'] = '<p>Hi {CONTACT_NAME}!</p><p>We just published your item ({ITEM_TITLE}) on {WEB_TITLE}.</p><p>{START_PUBLISH_FEE}</p><p>In order to make your ad available to anyone on {WEB_TITLE}, you should complete the process and pay the publish fee. You could do that on the following link: {PUBLISH_LINK}</p><p>{END_PUBLISH_FEE}</p><p>{START_PREMIUM_FEE}</p><p>You could make your ad premium and make it to appear on top result of the searches made on {WEB_TITLE}. You could do that on the following link: {PREMIUM_LINK}</p><p>{END_PREMIUM_FEE}</p><p>This is an automatic email, if you already did that, please ignore this email.</p><p>Thanks</p>';
            $res = Page::newInstance()->insert(
                array('s_internal_name' => 'payment_pro_email_payment', 'b_indelible' => '1'),
                $description
                );

        }

        public function premiumOff($id) {
            return $this->dao->delete($this->getTable_premium(), array('fk_i_item_id' => $id));
        }

        public function deleteItem($id) {
            $this->premiumOff($id);
            return $this->dao->delete($this->getTable_publish(), array('fk_i_item_id' => $id));
        }

        public function deletePrices($id) {
            return $this->dao->delete($this->getTable_prices(), array('fk_i_category_id' => $id));
        }

        public function disableItem($id) {
            return $this->dao->update($this->getTable_publish(), array('b_enabled' => 0, 'dt_date' => date("Y-m-d H:i:s")), array('fk_i_item_id' => $id));
        }

        public function enableItem($id) {
            return $this->dao->update($this->getTable_publish(), array('b_enabled' => 1, 'dt_date' => date("Y-m-d H:i:s")), array('fk_i_item_id' => $id));
        }

        public function uninstall()
        {

            $confPath = PAYMENT_PRO_PATH . 'payments/';
            $dir = opendir($confPath);
            while($file = readdir($dir)) {
                if(is_dir($confPath . $file) && $file!='.' && $file!='..') {
                    if(file_exists($confPath . $file . '/load.php')) {
                        include_once $confPath . $file . '/load.php';
                    }
                }
            }
            closedir($dir);

            osc_run_hook('payment_pro_pre_uninstall');


            $this->dao->query(sprintf('DROP TABLE %s', $this->getTable_premium()) ) ;
            $this->dao->query(sprintf('DROP TABLE %s', $this->getTable_publish()) ) ;
            $this->dao->query(sprintf('DROP TABLE %s', $this->getTable_wallet()) ) ;
            $this->dao->query(sprintf('DROP TABLE %s', $this->getTable_prices()) ) ;
            $this->dao->query(sprintf('DROP TABLE %s', $this->getTable_invoice_row()) ) ;
            $this->dao->query(sprintf('DROP TABLE %s', $this->getTable_invoice()) ) ;
            $this->dao->query(sprintf('DROP TABLE %s', $this->getTable_pending_row()) ) ;
            $this->dao->query(sprintf('DROP TABLE %s', $this->getTable_mail_queue()) ) ;

            $page = Page::newInstance()->findByInternalName('payment_pro_email_payment');
            Page::newInstance()->deleteByPrimaryKey($page['pk_i_id']);

            Preference::newInstance()->delete(array('s_section' => 'payment_pro'));

            osc_run_hook('payment_pro_post_uninstall');

        }

        public function getPaymentByCode($code, $source, $status = null) {
            $this->dao->select('*') ;
            $this->dao->from($this->getTable_invoice());
            $this->dao->where('s_code', $code);
            $this->dao->where('s_source', $source);
            if($status!=null) {
                $this->dao->where('i_status', $status);
            }
            $result = $this->dao->get();
            if($result) {
                return $result->row();
            }
            return false;
        }

        public function getPayment($invoiceId) {
            $this->dao->select('*') ;
            $this->dao->from($this->getTable_invoice());
            $this->dao->where('pk_i_id', $invoiceId);
            $result = $this->dao->get();
            if($result) {
                return $result->row();
            }
            return false;
        }

        public function getPublishData($itemId) {
            $this->dao->select('*') ;
            $this->dao->from($this->getTable_publish());
            $this->dao->where('fk_i_item_id', $itemId);
            $result = $this->dao->get();
            if($result) {
                $row = $result->row();
                if(isset($row['fk_i_item_id'])) {
                    return $row;
                }
            }
            return false;
        }

        public function getPremiumData($itemId) {
            $this->dao->select('*') ;
            $this->dao->from($this->getTable_premium());
            $this->dao->where('fk_i_item_id', $itemId);
            $result = $this->dao->get();
            if($result) {
                $row = $result->row();
                if(isset($row['fk_i_item_id'])) {
                    return $row;
                }
            }
            return false;
        }

        public function createItem($itemId, $paid = 0, $date = NULL, $invoiceId = NULL, $enabled = 1) {
            if($date==NULL) { $date = date("Y-m-d H:i:s"); };
            $this->dao->insert($this->getTable_publish(), array('fk_i_item_id' => $itemId, 'dt_date' => $date, 'b_paid' => $paid, 'b_enabled' => $enabled, 'fk_i_invoice_id' => $invoiceId));
        }

        public function getPublishPrice($categoryId) {
            if(osc_get_preference('pay_per_post', 'payment_pro')==0) { return 0; }
            $this->dao->select('*') ;
            $this->dao->from($this->getTable_prices());
            $this->dao->where('fk_i_category_id', $categoryId);
            $result = $this->dao->get();
            if($result) {
                $cat = $result->row();
                if(isset($cat['i_publish_cost'])) {
                    return $cat["i_publish_cost"];
                }
            }
            return osc_get_preference('default_publish_cost', 'payment_pro');
        }

        public function getPremiumPrice($categoryId) {
            if(osc_get_preference('allow_premium', 'payment_pro')==0) { return 0; }
            $this->dao->select('*') ;
            $this->dao->from($this->getTable_prices());
            $this->dao->where('fk_i_category_id', $categoryId);
            $result = $this->dao->get();
            if($result) {
                $cat = $result->row();
                if(isset($cat['i_premium_cost'])) {
                    return $cat["i_premium_cost"];
                }
            }
            return osc_get_preference('default_premium_cost', 'payment_pro');
        }

        public function getWallet($userId) {
            $this->dao->select('*') ;
            $this->dao->from($this->getTable_wallet());
            $this->dao->where('fk_i_user_id', $userId);
            $result = $this->dao->get();
            if($result) {
                $row = $result->row();
                $row['formatted_amount'] = (isset($row['i_amount'])?$row['i_amount']:0)/1000000;
                return $row;
            }
            return false;
        }

        public function getCategoriesPrices() {
            $this->dao->select('*') ;
            $this->dao->from($this->getTable_prices());
            $result = $this->dao->get();
            if($result) {
                return $result->result();
            }
            return array();
        }

        public function publishFeeIsPaid($itemId) {
            $this->dao->select('*') ;
            $this->dao->from($this->getTable_publish());
            $this->dao->where('fk_i_item_id', $itemId);
            $result = $this->dao->get();
            if($result) {
                $row = $result->row();
                if(isset($row['b_paid']) && $row['b_paid']==1) {
                    return true;
                }
            }
            return false;
        }

        public function premiumFeeIsPaid($itemId) {
            $this->dao->select('*') ;
            $this->dao->from($this->getTable_premium());
            $this->dao->where('fk_i_item_id', $itemId);
            $this->dao->where("dt_expiration_date >= '" . date('Y-m-d H:i:s') . "'");
            $result = $this->dao->get();
            if($result) {
                $row = $result->row();
                if (isset($row['dt_date'])) {
                    return true;
                }
            }
            return false;
        }

        public function isEnabled($itemId) {
            $this->dao->select('b_enabled') ;
            $this->dao->from($this->getTable_publish());
            $this->dao->where('fk_i_item_id', $itemId);
            $result = $this->dao->get();
            if($result) {
                $row = $result->row();
                if ($row) {
                    if (isset($row['b_enabled']) && $row['b_enabled'] == 1) {
                        return true;
                    }
                }
            }
            return false;
        }

        public function purgeExpired() {
            $this->dao->select("fk_i_item_id");
            $this->dao->from($this->getTable_premium());
            $time = time();
            $this->dao->where("dt_expiration_date >= '" . date('Y-m-d H:i:s', (int)osc_get_preference('last_purge', 'payment_pro')-3600) . "'");
            $this->dao->where("dt_expiration_date < '" . date('Y-m-d H:i:s') . "'");
            $result = $this->dao->get();
            if($result) {
                $items = $result->result();
                $mItem = new ItemActions(false);
                foreach($items as $item) {
                    $mItem->premium($item['fk_i_item_id'], false);
                    $this->premiumOff($item['fk_i_item_id']);
                };
            };
            osc_set_preference('last_purge', $time, 'payment_pro');
        }

        public function purgePending() {
            return $this->dao->delete($this->getTable_pending_row(), array("dt_date < '" . date('Y-m-d H:i:s', time()-(7*24*3600)) . "'"));
        }


        public function saveInvoice($code, $amount, $status, $currency, $email, $user, $source, $rows) {

            $this->dao->insert($this->getTable_invoice(), array(
                'dt_date' => date("Y-m-d H:i:s"),
                's_code' => $code,
                'i_amount' => $amount*1000000,
                's_currency_code' => $currency,
                's_email' => $email,
                'fk_i_user_id' => $user,
                's_source' => $source,
                'i_status' => $status
                ));
            $invoice_id = $this->dao->insertedId();
            foreach($rows as $row) {
                $this->dao->insert($this->getTable_invoice_row(), array(
                    'fk_i_invoice_id' => $invoice_id,
                    's_concept' => $row['description'],
                    'i_amount' => $row['amount']*1000000,
                    'i_quantity' => $row['quantity'],
                    'fk_i_item_id' => @$row['item_id'],
                    'i_product_type' => $row['id']
                ));
            }
            return $invoice_id;
        }

        public function pendingInvoice($rows) {
            $code = sha1(osc_genRandomPassword(40));
            while(true) {
                $this->dao->select("s_code");
                $this->dao->from($this->getTable_pending_row());
                $this->dao->where("s_code", $code);
                $this->dao->limit(1);
                $result = $this->dao->get();
                if($result) {
                    if($result->numRows()>0) {
                        $code = sha1(osc_genRandomPassword(40));
                    } else {
                        break;
                    }
                } else {
                    break;
                }
            }
            $date = date('Y-m-d H:i:s');
            foreach($rows as $row) {
                $this->dao->insert($this->getTable_pending_row(), array(
                    's_code' => $code,
                    'dt_date' => $date,
                    's_concept' => $row['description'],
                    'i_amount' => $row['amount']*1000000,
                    'i_quantity' => $row['quantity'],
                    'fk_i_item_id' => @$row['item_id'],
                    'i_product_type' => $row['id']
                ));
            }
            return $code;
        }

        public function getPending($code) {
            $this->dao->select("s_concept as description, i_amount as amount, i_quantity as quantity, i_product_type as id");
            $this->dao->from($this->getTable_pending_row());
            $this->dao->where("s_code", $code);
            $result = $this->dao->get();
            if($result) {
                return $result->result();
            }
            return array();
        }

        public function deletePending($code) {
            return $this->dao->delete($this->getTable_pending_row(), array('s_code' => $code));
        }

        public function invoices($params) {
            $start    = (isset($params['start']) && $params['start']!='' )     ? $params['start']: 0;
            $limit    = (isset($params['limit']) && $params['limit']!='' )      ? $params['limit']: 10;
            $status  = (isset($params['status']) && $params['status']!='')  ? $params['status'] : '';
            $source = (isset($params['source']) && $params['source']!='') ? $params['source'] : '';
            $this->dao->select('*') ;
            $this->dao->from($this->getTable_invoice());
            $this->dao->orderBy('dt_date', 'DESC');
            if($source!='') {
                $this->dao->where('s_source', $source);
            }
            if($status!='') {
                $this->dao->where('i_status', $status);
            }
            $this->dao->limit($limit, $start);
            $result = $this->dao->get();
            if($result) {
                return $result->result();
            }
            return array();
        }

        public function invoicesTotal() {
            $this->dao->select('COUNT(*) as total') ;
            $this->dao->from($this->getTable_invoice());
            $result = $this->dao->get();
            if($result) {
                $row = $result->row();
                if(isset($row['total'])) {
                    return $row['total'];
                }
            }
            return 0;
        }

        public function itemsByInvoice($id) {
            $this->dao->select('*') ;
            $this->dao->from($this->getTable_invoice_row());
            $this->dao->where('fk_i_invoice_id', $id);
            $this->dao->orderBy('pk_i_id', 'ASC');
            $result = $this->dao->get();
            if($result) {
                return $result->result();
            }
            return array();
        }

        public function insertPrice($category, $publish_fee, $premium_fee) {
            return $this->dao->replace($this->getTable_prices(), array('fk_i_category_id' => $category, 'i_publish_cost' => $publish_fee, 'i_premium_cost' => $premium_fee));
        }

        public function payPostItem($itemId) {
            return $this->dao->update($this->getTable_publish(), array('b_paid' => 1), array('fk_i_item_id' => $itemId));
        }

        public function unpayPublishFee($itemId) {
            $paid = $this->getPublishData($itemId);
            if (!$paid) {
                $this->createItem($itemId, 0, date("Y-m-d H:i:s"), 'ADMIN');
            } else {
                $this->dao->update($this->getTable_publish(), array('b_paid' => 0, 'dt_date' => date("Y-m-d H:i:s"), 'fk_i_invoice_id' => 'ADMIN'), array('fk_i_item_id' => $itemId));
            }
            $this->disableItem($itemId);
            $mItems = new ItemActions(false);
            $mItems->disable($itemId);
        }

        public function payPublishFee($itemId, $invoiceId) {
            if($this->isEnabled($itemId)) {
                $paid = $this->getPublishData($itemId);
                if (!$paid) {
                    $this->createItem($itemId, 1, date("Y-m-d H:i:s"), $invoiceId);
                } else {
                    $this->dao->update($this->getTable_publish(), array('b_paid' => 1, 'dt_date' => date("Y-m-d H:i:s"), 'fk_i_invoice_id' => $invoiceId), array('fk_i_item_id' => $itemId));
                }
                if (!OC_ADMIN) {
                    $this->updateQueue($itemId, false);
                }
                $mItems = new ItemActions(false);
                $mItems->enable($itemId);
                return PAYMENT_PRO_ENABLED;
            }
            return PAYMENT_PRO_DISABLED;
        }

        public function payPremiumFee($itemId, $invoiceId) {
            $paid = $this->getPremiumData($itemId);
            $exp_date = date('Y-m-d H:i:s', max(strtotime(@$paid['dt_expiration_date']), time())+((int)osc_get_preference('premium_days', 'payment_pro')*24*3600));
            if(!$paid) {
                $this->dao->insert($this->getTable_premium(), array('dt_date' => date("Y-m-d H:i:s"), 'dt_expiration_date' => $exp_date, 'fk_i_invoice_id' => $invoiceId, 'fk_i_item_id' => $itemId));
            } else {
                $this->dao->update($this->getTable_premium(), array('dt_date' => date("Y-m-d H:i:s"), 'dt_expiration_date' => $exp_date, 'fk_i_invoice_id' => $invoiceId), array('fk_i_item_id' => $itemId));
            }
            if(!OC_ADMIN) {
                $this->updateQueue($itemId, null, false);
            }
            $mItem = new ItemActions(false);
            return $mItem->premium($itemId, true);
        }

        public function addWallet($user, $amount) {
            $amount = (int)($amount*1000000);
            $wallet = $this->getWallet($user);
            if(isset($wallet['i_amount'])) {
                return $this->dao->update($this->getTable_wallet(), array('i_amount' => $amount+$wallet['i_amount']), array('fk_i_user_id' => $user));
            } else {
                return $this->dao->insert($this->getTable_wallet(), array('fk_i_user_id' => $user, 'i_amount' => $amount));
            }
        }

        public function getInvoiceSources() {
            $this->dao->select('s_source');
            $this->dao->from($this->getTable_invoice());
            $this->dao->groupBy('s_source');
            $result = $this->dao->get();
            if($result!==false) {
                return $result->result();
            }
            return array();
        }

        public function getQueue($date) {
            $this->dao->select('*');
            $this->dao->from($this->getTable_mail_queue());
            $this->dao->where("dt_send_date <= '" . $date . "'");
            $result = $this->dao->get();
            if($result!==false) {
                return $result->result();
            }
            return array();
        }

        public function addQueue($date, $item, $publish = true, $premium = true) {
            return $this->dao->insert(
                $this->getTable_mail_queue(),
                array(
                    'dt_send_date' => $date,
                    'fk_i_item_id' => $item,
                    'b_publish' => $publish,
                    'b_premium' => $premium
                )
            );
        }

        public function updateQueue($item, $publish = null, $premium = null) {
            $this->dao->select('*');
            $this->dao->from($this->getTable_mail_queue());
            $this->dao->where('fk_i_item_id', $item);
            $result = $this->dao->get();
            if($result!==false) {
                $queue = $result->row();
                if(isset($queue['b_publish']) && isset($queue['b_premium'])) {

                    $condition = array();
                    if ($publish != null) {
                        $condition[] = array('b_publish' => $publish);
                    }
                    if ($premium != null) {
                        $condition[] = array('b_premium' => $premium);
                    }
                    if ((($publish == null && $queue['b_publish'] == 0) || $publish == false) && (($premium == null && $queue['b_premium'] == 0) || $premium == false)) {
                        return $this->dao->delete($this->getTable_mail_queue(), array('fk_i_item_id' => $item));
                    } else {
                        return $this->dao->update($this->getTable_mail_queue(), $condition, array('fk_i_item_id' => $item));
                    }
                }
            }
            return false;
        }

        public function purgeQueue($date) {
            return $this->dao->delete($this->getTable_mail_queue(), array("dt_send_date <= '" . $date . "'"));
        }

    }

?>