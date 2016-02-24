<?php

/**
 * @link                : http://www.loginradius.com
 * @category            : LoginRadius_RaaS
 * @package             : RaaSAPI
 * @author              : LoginRadius Team
 * @license             : http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
require_once LR_RAAS_DIR . 'lib/RaaSAPI.php';

/**
 * Class app
 */
class CustomObject extends RaaSAPI {

    function get_custom_obj_by_accountid( $objectId, $accountId, $exist_check = false ) {
        
        $path = "/raas/v1/user/customObject";
        $parameter = array('objectid' => $objectId,'accountid' => $accountId);

        $response = json_decode( parent::api_client($path, $parameter) );

        if( $exist_check ) {
            if( isset( $response->CustomObject ) ) {
                return true;
            } else {
                return false;
            }
        }else {
            return $response;
        }
    }

    public function checkCustomObjectExists( $objectId, $accountId ) {
        $path = "/raas/v1/user/customObject/check";
        $parameter = array('objectid' => $objectId,'accountid' => $accountId);
        return parent::api_client( $path, $parameter );
    }

    /**
     * $objectId = 'xxxxxxxxxxxx',
     * $accountId = 'xxxxxxxxxxxx'
     * 
     * return all custom field
     * {
     *     "Id": "53e31d61164ff214a0814327",
     *     "IsActive": true,
     *     "DateCreated": "2014-08-07T06:32:01.016Z",
     *     "DateModified": "2014-08-07T09:09:21.08Z",
     *     "IsDeleted": true,
     *     "Uid": "676d5049aba24314b8a5c5af1b80c0cb",
     *     "CustomObject": {
     *         "Id": "53e30b2c164ff114a044f3f4",
     *         "IsActive": true,
     *         "DateCreated": "2014-08-07T05: 14: 20.573Z",
     *         "DateModified": "2014-08-07T05: 14: 20.573Z",        
     *         "IsDeleted": false,
     *         "Uid": "81ef41c461aa4a5eacba0a06f10c1481",
     *         "CustomObject": {
     *             "Industry": "chemical",
     *             "website": "http: //localhost23423423",
     *             "lastname": "",
     *             "RelationshipStatus": "married",
     *             "customfield1": {
     *                 "field1": "1",
     *                 "field2": "2",
     *                 "field5": "5",
     *                 "field6": "6"
     *             }
     *         }
     *     }
     * }
     * 
     */
    public function getObjectByAccountid($objectId, $accountId) {
        $path = "/raas/v1/user/customObject";
        $parameter = array('objectid' => $objectId,'accountid' => $accountId);
        return parent::api_client($path, $parameter);
    }

    /**
     * $objectid = 'xxxxxxxxxxxx';
     * $id = 'xxxxxxxxxxxx';
     * 
     * return all custom field
     * {
     *     "Id": "53e31d61164ff214a0814327",
     *     "IsActive": true,
     *     "DateCreated": "2014-08-07T06:32:01.016Z",
     *     "DateModified": "2014-08-07T09:09:21.08Z",
     *     "IsDeleted": true,
     *     "Uid": "676d5049aba24314b8a5c5af1b80c0cb",
     *     "CustomObject": {
     *         "Id": "53e30b2c164ff114a044f3f4",
     *         "IsActive": true,
     *         "DateCreated": "2014-08-07T05: 14: 20.573Z",
     *         "DateModified": "2014-08-07T05: 14: 20.573Z",
     *         "IsDeleted": false,
     *         "Uid": "81ef41c461aa4a5eacba0a06f10c1481",
     *         "CustomObject": {
     *             "Industry": "chemical",
     *             "website": "http: //localhost23423423",
     *             "lastname": "",
     *             "RelationshipStatus": "married",
     *             "customfield1": {
     *                 "field1": "1",
     *                 "field2": "2",
     *                 "field5": "5",
     *                 "field6": "6"
     *             }
     *         }
     *     }
     * }
     * 
     */
    public function getObjectByRecordid($objectId, $id) {
        $path = "/raas/v1/user/customObject";
        $parameter = array('objectid' => $objectId,'id' => $id);
        return parent::api_client($path, $parameter);
    }
    
    /**
     * $objectId = 'xxxxxxxxxxxx';
     * $accountIds = 'xxxxxxxxxxxx,xxxxxxxxxxxx,xxxxxxxxxxxx';
     * 
     * return all custom field
     * [{
     *     "Id": "53e31d61164ff214a0814327",
     *     "IsActive": true,
     *     "DateCreated": "2014-08-07T06:32:01.016Z",
     *     "DateModified": "2014-08-07T09:09:21.08Z",
     *     "IsDeleted": true,
     *     "Uid": "676d5049aba24314b8a5c5af1b80c0cb",
     *     "CustomObject": {
     *         "Id": "53e30b2c164ff114a044f3f4",
     *         "IsActive": true,
     *         "DateCreated": "2014-08-07T05: 14: 20.573Z",
     *         "DateModified": "2014-08-07T05: 14: 20.573Z",
     *         "IsDeleted": false,     * 
     *         "Uid": "81ef41c461aa4a5eacba0a06f10c1481",
     *         "CustomObject": {
     *             "Industry": "chemical",
     *             "website": "http: //localhost23423423",
     *             "lastname": "",     * 
     *             "RelationshipStatus": "married",
     *             "customfield1": {
     *                 "field1": "1",
     *                 "field2": "2",
     *                 "field5": "5",
     *                 "field6": "6"
     *             }
     *         }
     *     }
     * },
     * {
     *     "Id": "53e31d61164ff214a0814327",
     *     "IsActive": true,
     *     "DateCreated": "2014-08-07T06:32:01.016Z",
     *     "DateModified": "2014-08-07T09:09:21.08Z",
     *     "IsDeleted": true,
     *     "Uid": "676d5049aba24314b8a5c5af1b80c0cb",
     *     "CustomObject": {
     *         "Id": "53e30b2c164ff114a044f3f4",
     *         "IsActive": true,
     *         "DateCreated": "2014-08-07T05: 14: 20.573Z",
     *         "DateModified": "2014-08-07T05: 14: 20.573Z",
     *         "IsDeleted": false,
     *         "Uid": "81ef41c461aa4a5eacba0a06f10c1481",
     *         "CustomObject": {
     *             "Industry": "chemical",
     *             "website": "http: //localhost23423423",
     *             "lastname": "",
     *             "RelationshipStatus": "married",
     *             "customfield1": {
     *                 "field1": "1",
     *                 "field2": "2",
     *                 "field5": "5",
     *                 "field6": "6"
     *             }
     *         }
     *     }
     * }]
     * 
     */
    public function getObjectByAccountids($objectId, $accountIds) {
        $path = "/raas/v1/user/customObject";
        $parameter = array('objectid' => $objectId,'accountids' => $accountIds);
        return parent::api_client($path, $parameter);
    }

    /**
     * $objectId = 'xxxxxxxxxx';
     * $query = "<Expression LogicalOperation='AND'>
     *              <Field Name='Provider' ComparisonOperator='Equal'>facebook</Field>
     *              <Expression LogicalOperation='OR'>
     *                  <Field Name='Gender' ComparisonOperator='Equal'>M</Field>
     *                  <Field Name='Gender' ComparisonOperator='Equal'>U</Field>
     *              </Expression>
     *          </Expression>";
     * ------------------ OR ------------------
     * $query = "<Field Name='Gender' ComparisonOperator='Equal'>F</Field>";
     * 
     * $nextCursor=>[1]; (optional)
     * );
     * 
     * return all custom field
     * {    
     *     "Id": "53e31d61164ff214a0814327",
     *     "IsActive": true,
     *     "DateCreated": "2014-08-07T06:32:01.016Z",
     *     "DateModified": "2014-08-07T09:09:21.08Z",
     *     "IsDeleted": true,
     *     "Uid": "676d5049aba24314b8a5c5af1b80c0cb",
     *     "CustomObject": {
     *         "Id": "53e30b2c164ff114a044f3f4",
     *         "IsActive": true,
     *         "DateCreated": "2014-08-07T05: 14: 20.573Z",
     *         "DateModified": "2014-08-07T05: 14: 20.573Z",
     *         "IsDeleted": false,
     *         "Uid": "81ef41c461aa4a5eacba0a06f10c1481",
     *         "CustomObject": {
     *             "Industry": "chemical",
     *             "website": "http: //localhost23423423",
     *             "lastname": "",
     *             "RelationshipStatus": "married",
     *             "customfield1": {
     *                 "field1": "1",
     *                 "field2": "2",
     *                 "field5": "5",
     *                 "field6": "6"
     *             }
     *         }
     *     }
     * }
     */
    public function getObjectByQuery($objectId, $query, $nextCursor=1) {
        $path = "/raas/v1/user/customObject";
        $parameter = array('objectid'=> $objectId,'q'=>$query,'cursor'=>$nextCursor);
        return parent::api_client($path, $parameter);
    }

    /**
     * $obejctId = 'xxxxxxxxxx';
     * $nextCursor = [1]; (optional)
     * 
     * return 
     * {    
     *     "Id": "53e31d61164ff214a0814327",
     *     "IsActive": true,
     *     "DateCreated": "2014-08-07T06:32:01.016Z",
     *     "DateModified": "2014-08-07T09:09:21.08Z",
     *     "IsDeleted": true,
     *     "Uid": "676d5049aba24314b8a5c5af1b80c0cb",
     *     "CustomObject": {
     *         "Id": "53e30b2c164ff114a044f3f4",
     *         "IsActive": true,
     *         "DateCreated": "2014-08-07T05: 14: 20.573Z",
     *         "DateModified": "2014-08-07T05: 14: 20.573Z",
     *         "IsDeleted": false,
     *         "Uid": "81ef41c461aa4a5eacba0a06f10c1481",
     *         "CustomObject": {
     *             "Industry": "chemical",
     *             "website": "http: //localhost23423423",
     *             "lastname": "",
     *             "RelationshipStatus": "married",
     *             "customfield1": {
     *                 "field1": "1",
     *                 "field2": "2",
     *                 "field5": "5",
     *                 "field6": "6"
     *             }
     *         }
     *     }
     * }
     */
    
    public function getAllObject($objectId, $nextCursor=1) {
        $path = "/raas/v1/user/customObject";
        $parameter = array('objectid'=> $objectId, 'cursor'=>$nextCursor);
        return parent::api_client($path, $parameter);
    }

    /**
     * $objectId = 'xxxxxxxxxx';
     * 
     * return 
     * {
     *     "TotalUsedMemory": 0.01,
     *     "RemainingMemory": 9.99,
     *     "TotalRecords": 7
     * }
     * 
     */
    public function getStats($objectId) {
        $path = "/raas/v1/user/customObject/stats";
        $parameter = array('objectid' => $objectId);
        return parent::api_client($path, $parameter);
    }

    /**
     * $objectId = 'xxxxxxxxxx';
     * $accountId = 'xxxxxxxxxx';
     * $data = array(
     *  firstname => 'first name',
     *  lastname => 'last name',
     *  gender => 'm',
     *  birthdate => 'MM-DD-YYYY',
     *  ....................
     *  ....................
     * );
     * 
     * return { “isPosted” : true }
     */
    public function upsert ($objectId, $accountId, $data) {
        $path = "/raas/v1/user/customObject/upsert";
        $parameter = array('objectid' => $objectId,'accountid' => $accountId);
        return parent::api_client($path, $parameter, json_encode($data), 'application/json');
    }

    /**
     * $objectId = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
     * $accountId = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
     * $action = true/false(boolean)
     * 
     * return { “isPosted” : true }
     */
    public function setStatus($objectId, $accountId, $action = true) {
        $path = "/raas/v1/user/customObject/status";
        $parameter = array('objectid' => $objectId,'accountid' => $accountId);
        return parent::api_client($path, $parameter, array('isblock' => $action));
    }

}
