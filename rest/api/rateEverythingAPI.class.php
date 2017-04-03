<?php

require_once 'api.class.php';

/**
 * The basic methodology will be:
 * Data retrieval via Curl
 * Data posting via Ajax.
 *
 * If you need to test the API to validate data I suggest the following chrome plugin, works very well:
 * https://chrome.google.com/webstore/detail/advanced-rest-client/hgmloofddffdnphfgcellkdfbfbjeloo?utm_source=chrome-ntp-icon
 */
class RateEverythingAPI extends API
{
    /**
     * @internal
     */
    protected $User;
    protected $db;

    /**
     * @internal
     */
    public function __construct($request, $origin)
    {
        parent::__construct($request);
        $this->db = new Db();
    }

    /**
     * @internal
     */
    protected function _get_full_ip()
    {
        // Get Real IP
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $ip = explode(',', $ip);
        // get server ip and resolved it
        $FIRE_IP_ADDR = $_SERVER['REMOTE_ADDR'];
        $ip_resolved = gethostbyaddr($FIRE_IP_ADDR);

        // builds server ip infos string
        if ($FIRE_IP_ADDR != $ip_resolved && $ip_resolved) {
            $FIRE_IP_LITT = $FIRE_IP_ADDR.' - '.$ip_resolved;
        } else {
            $FIRE_IP_LITT = $FIRE_IP_ADDR;
        }

        // builds client ip full infos string
        if ($ip[0] != $FIRE_IP_ADDR) {
            $full_ip_info = "{$ip[0]} | $FIRE_IP_LITT";
        } else {
            $full_ip_info = $FIRE_IP_LITT;
        }

        return $full_ip_info;
    }

    /**
     * @api GET /entityList Get entity list
     * @apiName entityList
     * @apiSuccess {String} status Request Status {Success/Fail}.
     * @apiSuccess {Array} entities List of entities
     */
    protected function entityList()
    {
        if ($this->method == 'GET') {
            $entityquery = 'SELECT id as entityId, name, description FROM entities';
            $entityresult = $this->db->execute($entityquery);
            if ($entityresult !== false) {
                if ($this->db->count() > 0) {
                    return json_encode(array('entities' => $entityresult,
                    'status' => 'success',
                    'response' => 'Entities loaded successfully', ));
                } else {
                    throw new Exception('Wrong entity count for query');
                }
            } else {
                throw new Exception('Database error when retrieving entities');
            }
        } else {
            throw new Exception('Only accepts GET requests');
        }
    }

    /**
     * @api GET /entityRating get entity rating
     * @apiName entityRating
     * @requires entityId (passed as url endpoint)
     * @apiSuccess {String} status Request Status {Success/Fail}.
     * @apiSuccess {String} entityRating Entity Rating
     */
    protected function entityRating()
    {
        if ($this->method == 'GET') {
            $entityId = isset($this->args[0]) && is_numeric($this->args[0]) ? $this->args[0] : 0;
            $ratingquery = "SELECT rating, username, userip, userinfo FROM entityrating where entityId='{$entityId}'";
            $ratingresult = $this->db->execute($ratingquery);
            if ($ratingresult !== false) {
                if ($this->db->count() > 0) {
                    foreach ($ratingresult as $key => $row) {
                        $ratingresult['total'] += $row['rating'];
                    }

                    return json_encode(array('rating' => $ratingresult,
                    'status' => 'success',
                    'response' => 'Rating loaded successfully', ));
                } else {
                    throw new Exception('Wrong rating count for query');
                }
            } else {
                throw new Exception('Database error when retrieving rating');
            }
        } else {
            throw new Exception('Only accepts GET requests');
        }
    }

    /**
     * @api POST /addEntityRating get entity rating
     * @apiName addEntityRating
     * @requires entityId
     * @requires rating
     * @requires username
     * @requires userip
     * @requires userinfo
     * @apiSuccess {String} status Request Status {Success/Fail}.
     */
    protected function addEntityRating()
    {
        if ($this->method == 'POST') {
            $entityId = $this->request['entityId'];
            $rating = $this->request['rating'];
            $username = $this->request['username'];
            $userip = $this->_get_full_ip();
            $userinfo = $_SERVER['HTTP_USER_AGENT'];
            $ratingquery = "INSERT INTO entityrating (entityId,rating, username, userip, userinfo) values ('{$entityId}','{$rating}','{$username}','{$userip}','{$userinfo}')";
            $ratingresult = $this->db->execute($ratingquery);
            if ($ratingresult !== false) {
                if ($this->db->affectedRows() > 0) {
                    return json_encode(array(
                    'status' => 'success',
                    'response' => 'Rating saved successfully', ));
                } else {
                    throw new Exception('Error saving rating');
                }
            } else {
                throw new Exception('Database error when saving rating');
            }
        } else {
            throw new Exception('Only accepts POST requests');
        }
    }
}
