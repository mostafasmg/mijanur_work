<?php
// データベースアクセスクラス(Base)
class CLS_Db_Ex
{
    protected static $dbinfo = array();
    
    // DBコネクション
    public $Conn;
    
    // 事前バインド用
    private $BindParams = array();
    
    // 事前バインド実行フラグ
    private $BindStartFlg;
    
    /********************************************************************/
    /* static Method                                                    */
    /********************************************************************/
    // コネクションのオープン
    public static function OpenConnection()
    {
        $dsn = sprintf("mysql:host=%s;dbname=%s;charset=%s", self::$dbinfo['url'], self::$dbinfo['db'], self::$dbinfo['charset']);
        $user = self::$dbinfo['user'];
        $pass = self::$dbinfo['pass'];
        
//        $options = array(PDO::MYSQL_ATTR_READ_DEFAULT_FILE => '/etc/my.cnf',);
//        $conn = new PDO($dsn, $user, $pass, $options);
        
        $conn = new PDO($dsn, $user, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    }
    
    // トランザクションの開始
    public static function BeginTransaction($conn)
    {
        $conn->beginTransaction();
    }
    
    // トランザクションのコミット
    public static function Commit($conn)
    {
        $conn->commit();
    }
    
    // トランザクションのロールバック
    public static function Rollback($conn)
    {
        $conn->rollBack();
    }
    
    // コネクションのクローズ
    public static function CloseConnection()
    {
        return null;
    }
    
    /********************************************************************/
    /* public Method                                                    */
    /********************************************************************/
    // コンストラクタ
    public function __construct($conn)
    {
        $this->Conn = $conn;
        
        // バインドフラグを落とす
        $this->BindStartFlg = false;
    }
    
    // コネクションのクローズ
    public function Close()
    {
        // コネクションのクローズ
        $this->Conn = null;
    }
    
    // 事前バインド開始メソッド
    public function BeginInAdvanceBindParam()
    {
        // バインドフラグを立てる
        $this->BindStartFlg = true;
        
        // バインドパラム用の連想配列初期化
        unset($this->BindParams);
    }
    
    // 事前バインド用メソッド
    public function InAdvanceBindParam($name, $variable, $type)
    {
        // バインドフラグが立っていないときは準備不足
        if (!$this->BindStartFlg)
        {
            // SQLエラーを考慮してここでNGにしておく
            echo "BIND FLG ERROR";
            exit();
        }
        
        // バインドパラムに値をセット
        $count = count($this->BindParams);
        $this->BindParams[$count]["name"] = $name;
        $this->BindParams[$count]["variable"] = $variable;
        $this->BindParams[$count]["type"] = $type;
    }
    
    /********************************************************************/
    /* Protected Method                                                 */
    /********************************************************************/
    protected function sql_run_select($sth)
    {
        // SQLの実行
        try
        {
            $result = $sth->execute();
        }
        catch (PDOException $ex)
        {
    		if (DEBUG_FLG == 1)
    		{
                echo "SQL ERROR";
    		    echo "<br>";
    		    echo $sth->queryString;
    		}
    		
    		// 例外をスロー
    		throw $ex;
        }
        
        // バインドフラグを落とす
        $this->BindStartFlg = false;
        
        // 結果を取得
        return $sth->fetchAll();
    }
    
    protected function sql_exec($sth)
    {
        // SQLの実行
        try
        {
            $result = $sth->execute();
        }
        catch (PDOException $ex)
        {
    		if (DEBUG_FLG == 1)
    		{
                echo "SQL ERROR";
    		    echo "<br>";
    		    echo $sth->queryString;
    		}
    		
    		// 例外をスロー
    		throw $ex;
        }
        
        // バインドフラグを落とす
        $this->BindStartFlg = false;
        
        // 成功ならSQLの実行結果件数を返す
        return $sth->rowCount();
    }
    
    // プリペアドステートメントの準備
    protected function sql_prepare($query)
    {
        $result = $this->Conn->prepare($query);
        if ($result === false)
        {
            // 正常なSQLではなかった
            echo "SQL ERROR";
    		if (DEBUG_FLG == 1)
    		{
    		    echo "<br>{$query}";
    		}
            exit();
        }
        
        return $result;
    }
    
    // 事前バインドパラムをセットして返す
    protected function SetInAdvanceBindParams($sth)
    {
        // バインドフラグが立っていないときはセットすべきパラメータが存在しないので抜ける
        if (!$this->BindStartFlg)
        {
        	return $sth;
        }
    	
        $count = count($this->BindParams);
        if ($count == 0)
        {
            return $sth;
        }
        
        // 連想配列の中身をハンドラに追加
        for ($i = 0; $i < $count; $i++)
        {
            $sth->bindParam($this->BindParams[$i]["name"], $this->BindParams[$i]["variable"], $this->BindParams[$i]["type"]);
        }
        
        return $sth;
    }
    
    /********************************************************************/
    /* Private Method                                                   */
    /********************************************************************/
}
