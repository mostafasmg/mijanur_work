<?php
require_once("CLS_Page_Ex.php");
require_once(dirname(__FILE__) . "/../Db/CLS_Db_CustomMenu.php");

/********************************************************************/
/* Ver.20151215                                                     */
/* ---------------------------------------------------------------- */
/* ◆ ◆ ◆ 編 集 禁 止 ◆ ◆ ◆                                    */
/* 編集する場合は上長の承認が必要です。                             */
/* ---------------------------------------------------------------- */
/* This file is not editable.                                       */
/********************************************************************/
// オンラインクラス(Base)
class CLS_Online_Ex extends CLS_Page_Ex
{
    /********************************************************************/
    /* Public Method                                                   */
    /********************************************************************/
    // index.php
    public function index()
    {
        // システム変数の置換
        $contents = $this->replace_template_sys("online/index.html");
        
        // カスタムメニューの生成
        $contents = $this->CreateCustomMenu($contents);
        
        // 表示ページの制御
        $catid = 1;
        if (isset($this->get["catid"]))
        {
            $catid = $this->get["catid"];
        }
        if (strlen($catid) <= 0)
        {
            $catid = 1;
        }
        $contents = str_replace("++[lcl_catid]", $catid, $contents);
        
        echo $contents;
    }
    
    /********************************************************************/
    /* protected Method                                                   */
    /********************************************************************/
    // カスタムメニューの生成
    protected function CreateCustomMenu($contents)
    {
        // 出力テンプレートの取得
        $temp_category = file_get_contents(dirname(__FILE__) . "/../../template/{$this->Mobile}tpl/tags/custmenu_category.tpl");
        $temp_group = file_get_contents(dirname(__FILE__) . "/../../template/{$this->Mobile}tpl/tags/custmenu_group.tpl");
        $temp_function = file_get_contents(dirname(__FILE__) . "/../../template/{$this->Mobile}tpl/tags/custmenu_function.tpl");
        
        // DBコネクションの生成
        $conn = CLS_Db::OpenConnection();
        
        // ログインユーザーのロールIDから表示メニューを取得する
        $objMenu = new CLS_Db_CustomMenu($conn);
        $tbl_catlist = $objMenu->GetCustomMenuCategory($_SESSION["l"]["roleid"]);
        
        $custom_menus = "";
        $count = count($tbl_catlist);
        // カテゴリーループ
        for ($i = 0; $i < $count; $i++)
        {
            // カテゴリーテンプレートをセット
            $work_category = $temp_category;
            
            // DB値の取得
            $categoryid = $tbl_catlist[$i]["categoryid"];
            $catname = $tbl_catlist[$i]["dispname"];
            
            // カテゴリ名の置換
            $work_category = str_replace("++[sys_catname]", $catname, $work_category);
            $work_category = str_replace("++[sys_catid]", $categoryid, $work_category);
            
            // カテゴリー毎のグループ一覧取得
            $grpTable = $objMenu->GetCustomMenuGroup($_SESSION["l"]["roleid"], $categoryid);
            
            $groups = "";
            $countGrp = count($grpTable);
            for ($k = 0; $k < $countGrp; $k++)
            {
                // グループテンプレートをセット
                $work_group = $temp_group;
                
                // DB値の取得
                $groupid = $grpTable[$k]["groupid"];
                $grpname = $grpTable[$k]["dispname"];
                
                // グループ名の置換
                $work_group = str_replace("++[sys_grpname]", $grpname, $work_group);
                
                // カテゴリー毎のファンクション一覧取得
                $table = $objMenu->GetCustomMenu($_SESSION["l"]["roleid"], $categoryid, $groupid);
                
                $functions = "";
                $count2 = count($table);
                for ($j = 0; $j < $count2; $j++)
                {
                    // ファンクションテンプレートをセット
                    $work_function = $temp_function;
                    
                    // DB値の取得
                    $categoryid = $table[$j]["categoryid"];
                    $catname = $table[$j]["catname"];
                    $functionid = $table[$j]["functionid"];
                    $fncname = $table[$j]["fncname"];
                    $linkurl = $table[$j]["linkurl"];
                    $imageurl = $table[$j]["imageurl"];
                    $imageurlpath = $this->dir_offset . "image/menuicon/{$imageurl}";
                    
                    // 表示値の置換
                    $work_function = str_replace("++[sys_linkurl]", $linkurl, $work_function);
                    $work_function = str_replace("++[sys_imageurl]", $imageurlpath, $work_function);
                    $work_function = str_replace("++[sys_fncname]", $fncname, $work_function);
                    
                    // ICONが登録されていなければエリア毎削除
                    if (strlen($imageurl) == 0)
                    {
                        $work_function = $this->PregMatchAreaHidden($work_function, "MENUICON");
                    }
                    
                    $functions .= $work_function;
                }
                
                // 取得完了したファンクション一覧をグループテンプレートに置換
                $work_group = str_replace("++[sys_functions]", $functions, $work_group);
                
                $groups .= $work_group;
            }
            
            
            // 取得完了したグループ一覧をカテゴリーテンプレートに置換
            $work_category = str_replace("++[sys_groups]", $groups, $work_category);
            
            // カスタムメニューの結合
            $custom_menus .= $work_category;
        }
        
        // 表示メニューの置換
        $contents = str_replace("++[sys_custom_menu]", $custom_menus, $contents);
        
        return $contents;
    }
}
