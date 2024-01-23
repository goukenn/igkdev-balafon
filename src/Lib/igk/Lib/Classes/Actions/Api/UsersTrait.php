<?php
// @author: C.A.D. BONDJE DOUE
// @file: UsersTrait.php
// @date: 20230505 11:01:10
namespace IGK\Actions\Api;

use IGK\Actions\Api\FormData\UserApiChangePwdFormData;
use IGK\Database\DbExpression;
use IGK\Helper\JSon;
use IGK\Models\ModelBase;
use IGK\Models\Users;
use IGK\System\Database\DbConditionExpressionBuilder;
use IGK\System\Http\ApiResponse;
use IGK\System\Http\Request;
use IGKException;

///<summary></summary>
/**
 * 
 * @package IGK\Actions\Api
 */
trait UsersTrait
{
    /**
     * get system's user info
     * @responses()
     * @security(['BearerAuth'])
     * @return array 
     */
    public function users_get()
    {
        $page_limit = 20;
        $p = igk_getr("p", 1);
        if ($p > 1) {
            $limit = [$p * $page_limit, $page_limit];
        } else {
            $limit = $p * $page_limit;
        }
        $conditions = [];
        $columns = Users::queryColumns();
        unset($columns[Users::column('clPwd')]);
        $columns = explode("|", str_replace(Users::table() . '.', '', implode("|", array_keys($columns))));

        $data = Users::select_all($conditions, ['Limit' => $limit, 'Columns' => $columns]);
        return [
            "page" => $p,
            "total" => Users::count($conditions),
            "limit" => $page_limit,
            "data" => $data
        ];
    }

    /**
     * block user
     * @param Users $user 
     * @return Users 
     */
    public function block_post(Users $user)
    {
        $user->clStatus = 0;
        unset($user->clPwd);
        $user->save();
        return $user;
    }
    /**
     * enable user's post
     * @param Users $user 
     * @return Users 
     */
    public function enabled_post(Users $user)
    {
        $user->clStatus = 1;
        unset($user->clPwd);
        $user->save();
        return $user;
    }
    /**
     * delete user 
     * @param Users $user 
     * @return Users 
     */
    public function delete_post(Users $user)
    {
        $user->clStatus = -1;
        $user->clDeactivate_At = new DbExpression(Users::FC_NOW);
        unset($user->clPwd);
        $user->save();
        return $user;
    }
    /**
     * delete user with DELETE verbs
     * @param Users $user 
     * @return Users 
     */
    public function index_delete(Users $user)
    {
        return $this->delete_post($user);
    }
    /**
     * search fur user with request query
     * @param string $query 
     * @return array 
     */
    public function search_get(string $query)
    {
        $query = '%' . trim($query, ' %') . '%';
        $conditions = [
            (new DbConditionExpressionBuilder(DbConditionExpressionBuilder::OP_OR))
                ->add("@@" . Users::FD_CL_LOGIN, $query)
                ->add("@@" . Users::FD_CL_FIRST_NAME, $query)
                ->add("@@" . Users::FD_CL_LAST_NAME, $query)
        ];
        return $this->_getPagerResult(Users::model(), $conditions, igk_getr("p", 1), 20);
    }
    private function _getPagerResult(ModelBase $model, $conditions, $p, $page_limit)
    {
        if ($p > 1) {
            $limit = [$p * $page_limit, $page_limit];
        } else {
            $limit = $p * $page_limit;
        }
        return [
            "page" => $p,
            "total" => $model::count($conditions),
            "limit" => $page_limit,
            "data" => $model::select_all($conditions, ['Limit' => $limit]),
        ];
    }

    /**
     * change user password
     * @param Request $request 
     * @param ApiResponse $response 
     * @param null|Users $user 
     * @return Users|void 
     * @throws IGKException 
     */
    public function changePassword_post(Request $request, ApiResponse $response, ?Users $user = null)
    {
        if (!is_null($user)) {
            if ($r = UserApiChangePwdFormData::ValidateJSon($request, $this->getValidator(), $errors)) {
                $user->clPwd = $r->password;
                $user->save();
                unset($user->clPwd);
                return $user;
            } else {
                $response->die("password missing requirement", 500);
            }
        }
        $response->die("user not found");
    }
}
