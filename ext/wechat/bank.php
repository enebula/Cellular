<?php
/**
 * Cellular Framework
 * 微信支付银行
 * @copyright Cellular Team
 */
namespace ext\wechat;
class Bank
{
    public static function bankName($id)
    {
        switch ($id) {
            case 'ICBC_DEBIT':
                return '工商银行(借记卡)';
                break;
            case 'ICBC_CREDIT':
                return '工商银行(信用卡)';
                break;
            case 'ABC_DEBIT':
                return '农业银行(借记卡)';
                break;
            case 'ABC_CREDIT':
                return '农业银行(信用卡)';
                break;
            case 'PSBC_DEBIT':
                return '邮政储蓄银行(借记卡)';
                break;
            case 'PSBC_CREDIT':
                return '邮政储蓄银行(信用卡)';
                break;
            case 'CCB_DEBIT':
                return '建设银行(借记卡)';
                break;
            case 'CCB_CREDIT':
                return '建设银行(信用卡)';
                break;
            case 'CMB_DEBIT':
                return '招商银行(借记卡)';
                break;
            case 'CMB_CREDIT':
                return '招商银行(信用卡)';
                break;
            case 'BOC_DEBIT':
                return '中国银行(借记卡)';
                break;
            case 'BOC_CREDIT':
                return '中国银行(信用卡)';
                break;
            case 'COMM_DEBIT':
                return '交通银行(借记卡)';
                break;
            case 'SPDB_DEBIT':
                return '浦发银行(借记卡)';
                break;
            case 'SPDB_CREDIT':
                return '浦发银行(信用卡)';
                break;
            case 'GDB_DEBIT':
                return '广发银行(借记卡)';
                break;
            case 'GDB_CREDIT':
                return '广发银行(信用卡)';
                break;
            case 'CMBC_DEBIT':
                return '民生银行(借记卡)';
                break;
            case 'CMBC_CREDIT':
                return '民生银行(信用卡)';
                break;
            case 'PAB_DEBIT':
                return '平安银行(借记卡)';
                break;
            case 'PAB_CREDIT':
                return '平安银行(信用卡)';
                break;
            case 'CEB_DEBIT':
                return '光大银行(借记卡)';
                break;
            case 'CEB_CREDIT':
                return '光大银行(信用卡)';
                break;
            case 'CIB_DEBIT':
                return '兴业银行(借记卡)';
                break;
            case 'CIB_CREDIT':
                return '兴业银行(信用卡)';
                break;
            case 'CITIC_DEBIT':
                return '中信银行(借记卡)';
                break;
            case 'CITIC_CREDIT':
                return '中信银行(信用卡)';
                break;
            case 'BOSH_DEBIT':
                return '上海银行(借记卡)';
                break;
            case 'BOSH_CREDIT':
                return '上海银行(信用卡)';
                break;
            case 'CRB_DEBIT':
                return '华润银行(借记卡)';
                break;
            case 'HZB_DEBIT':
                return '杭州银行(借记卡)';
                break;
            case 'HZB_CREDIT':
                return '杭州银行(信用卡)';
                break;
            case 'BSB_DEBIT':
                return '包商银行(借记卡)';
                break;
            case 'BSB_CREDIT':
                return '包商银行(信用卡)';
                break;
            case 'CQB_DEBIT':
                return '重庆银行(借记卡)';
                break;
            case 'SDEB_DEBIT':
                return '顺德农商行(借记卡)';
                break;
            case 'SZRCB_DEBIT':
                return '深圳农商银行(借记卡)';
                break;
            case 'HRBB_DEBIT':
                return '哈尔滨银行(借记卡)';
                break;
            case 'BOCD_DEBIT':
                return '成都银行(借记卡)';
                break;
            case 'GDNYB_DEBIT':
                return '南粤银行(借记卡)';
                break;
            case 'GDNYB_CREDIT':
                return '南粤银行(信用卡)';
                break;
            case 'GZCB_DEBIT':
                return '广州银行(借记卡)';
                break;
            case 'GZCB_CREDIT':
                return '广州银行(信用卡)';
                break;
            case 'JSB_DEBIT':
                return '江苏银行(借记卡)';
                break;
            case 'JSB_CREDIT':
                return '江苏银行(信用卡)';
                break;
            case 'NBCB_DEBIT':
                return '宁波银行(借记卡)';
                break;
            case 'NBCB_CREDIT':
                return '宁波银行(信用卡)';
                break;
            case 'NJCB_DEBIT':
                return '南京银行(借记卡)';
                break;
            case 'JZB_DEBIT':
                return '晋中银行(借记卡)';
                break;
            case 'KRCB_DEBIT':
                return '昆山农商(借记卡)';
                break;
            case 'LJB_DEBIT':
                return '龙江银行(借记卡)';
                break;
            case 'LNNX_DEBIT':
                return '辽宁农信(借记卡)';
                break;
            case 'LZB_DEBIT':
                return '兰州银行(借记卡)';
                break;
            case 'WRCB_DEBIT':
                return '无锡农商(借记卡)';
                break;
            case 'ZYB_DEBIT':
                return '中原银行(借记卡)';
                break;
            case 'ZJRCUB_DEBIT':
                return '浙江农信(借记卡)';
                break;
            case 'WZB_DEBIT':
                return '温州银行(借记卡)';
                break;
            case 'XAB_DEBIT':
                return '西安银行(借记卡)';
                break;
            case 'JXNXB_DEBIT':
                return '江西农信(借记卡)';
                break;
            case 'NCB_DEBIT':
                return '宁波通商银行(借记卡)';
                break;
            case 'NYCCB_DEBIT':
                return '南阳村镇银行(借记卡)';
                break;
            case 'NMGNX_DEBIT':
                return '内蒙古农信(借记卡)';
                break;
            case 'SXXH_DEBIT':
                return '陕西信合(借记卡)';
                break;
            case 'SRCB_CREDIT':
                return '上海农商银行(信用卡)';
                break;
            case 'SJB_DEBIT':
                return '盛京银行(借记卡)';
                break;
            case 'SDRCU_DEBIT':
                return '山东农信(借记卡)';
                break;
            case 'SRCB_DEBIT':
                return '上海农商银行(借记卡)';
                break;
            case 'SCNX_DEBIT':
                return '四川农信(借记卡)';
                break;
            case 'QLB_DEBIT':
                return '齐鲁银行(借记卡)';
                break;
            case 'QDCCB_DEBIT':
                return '青岛银行(借记卡)';
                break;
            case 'PZHCCB_DEBIT':
                return '攀枝花银行(借记卡)';
                break;
            case 'ZJTLCB_DEBIT':
                return '浙江泰隆银行(借记卡)';
                break;
            case 'TJBHB_DEBIT':
                return '天津滨海农商行(借记卡)';
                break;
            case 'WEB_DEBIT':
                return '微众银行(借记卡)';
                break;
            case 'YNRCCB_DEBIT':
                return '云南农信(借记卡)';
                break;
            case 'WFB_DEBIT':
                return '潍坊银行(借记卡)';
                break;
            case 'WHRC_DEBIT':
                return '武汉农商行(借记卡)';
                break;
            case 'ORDOSB_DEBIT':
                return '鄂尔多斯银行(借记卡)';
                break;
            case 'XJRCCB_DEBIT':
                return '新疆农信银行(借记卡)';
                break;
            case 'ORDOSB_CREDIT':
                return '鄂尔多斯银行(信用卡)';
                break;
            case 'CSRCB_DEBIT':
                return '常熟农商银行(借记卡)';
                break;
            case 'JSNX_DEBIT':
                return '江苏农商行(借记卡)';
                break;
            case 'GRCB_CREDIT':
                return '广州农商银行(信用卡)';
                break;
            case 'GLB_DEBIT':
                return '桂林银行(借记卡)';
                break;
            case 'GDRCU_DEBIT':
                return '广东农信银行(借记卡)';
                break;
            case 'GDHX_DEBIT':
                return '广东华兴银行(借记卡)';
                break;
            case 'FJNX_DEBIT':
                return '福建农信银行(借记卡)';
                break;
            case 'DYCCB_DEBIT':
                return '德阳银行(借记卡)';
                break;
            case 'DRCB_DEBIT':
                return '东莞农商行(借记卡)';
                break;
            case 'CZCB_DEBIT':
                return '稠州银行(借记卡)';
                break;
            case 'CZB_DEBIT':
                return '浙商银行(借记卡)';
                break;
            case 'CZB_CREDIT':
                return '浙商银行(信用卡)';
                break;
            case 'GRCB_DEBIT':
                return '广州农商银行(借记卡)';
                break;
            case 'CSCB_DEBIT':
                return '长沙银行(借记卡)';
                break;
            case 'CQRCB_DEBIT':
                return '重庆农商银行(借记卡)';
                break;
            case 'CBHB_DEBIT':
                return '渤海银行(借记卡)';
                break;
            case 'BOIMCB_DEBIT':
                return '内蒙古银行(借记卡)';
                break;
            case 'BOD_DEBIT':
                return '东莞银行(借记卡)';
                break;
            case 'BOD_CREDIT':
                return '东莞银行(信用卡)';
                break;
            case 'BOB_DEBIT':
                return '北京银行(借记卡)';
                break;
            case 'BNC_DEBIT':
                return '江西银行(借记卡)';
                break;
            case 'BJRCB_DEBIT':
                return '北京农商行(借记卡)';
                break;
            case 'AE_CREDIT':
                return 'AE(信用卡)';
                break;
            case 'GYCB_CREDIT':
                return '贵阳银行(信用卡)';
                break;
            case 'JSHB_DEBIT':
                return '晋商银行(借记卡)';
                break;
            case 'JRCB_DEBIT':
                return '江阴农商行(借记卡)';
                break;
            case 'JNRCB_DEBIT':
                return '江南农商(借记卡)';
                break;
            case 'JLNX_DEBIT':
                return '吉林农信(借记卡)';
                break;
            case 'JLB_DEBIT':
                return '吉林银行(借记卡)';
                break;
            case 'JJCCB_DEBIT':
                return '九江银行(借记卡)';
                break;
            case 'HXB_DEBIT':
                return '华夏银行(借记卡)';
                break;
            case 'HXB_CREDIT':
                return '华夏银行(信用卡)';
                break;
            case 'HUNNX_DEBIT':
                return '湖南农信(借记卡)';
                break;
            case 'HSB_DEBIT':
                return '徽商银行(借记卡)';
                break;
            case 'HSBC_DEBIT':
                return '恒生银行(借记卡)';
                break;
            case 'HRXJB_DEBIT':
                return '华融湘江银行(借记卡)';
                break;
            case 'HNNX_DEBIT':
                return '河南农信(借记卡)';
                break;
            case 'HKBEA_DEBIT':
                return '东亚银行(借记卡)';
                break;
            case 'HEBNX_DEBIT':
                return '河北农信(借记卡)';
                break;
            case 'HBNX_DEBIT':
                return '湖北农信(借记卡)';
                break;
            case 'HBNX_CREDIT':
                return '湖北农信(信用卡)';
                break;
            case 'GYCB_DEBIT':
                return '贵阳银行(借记卡)';
                break;
            case 'GSNX_DEBIT':
                return '甘肃农信(借记卡)';
                break;
            case 'JCB_CREDIT':
                return 'JCB(信用卡)';
                break;
            case 'MASTERCARD_CREDIT':
                return 'MASTERCARD(信用卡)';
                break;
            case 'VISA_CREDIT':
                return 'VISA(信用卡)';
                break;
            default:
                return '未知银行';
                break;
        }
    }
}