<?php

/**
 * Author: HL
 * Date: 2017-02-20
 * Time: 15:07
 */
class Booklist extends medoo
{
    private $bookid;
    private $bookname;
	private $booknum;
    private $bookauthor;
	private $bookurl;
    private $bookstatus;

    private $borrowid;
    private $borrowname;
    private $borrowtime;
    private $returntime;

	
	public function __construct($obj=null) {
		 parent::__construct();
		 try{
			 if($obj){
				 $this->bookid = isset($obj->{"bookid"})?$obj->{"bookid"}:0;
				 $this->bookname = isset($obj->{"bookname"})?$obj->{"bookname"}:null;
				 $this->booknum = isset($obj->{"booknum"})?$obj->{"booknum"}:0;
				 $this->bookauthor = isset($obj->{"bookauthor"})?$obj->{"bookauthor"}:null;
				 $this->bookurl = isset($obj->{"bookurl"})?$obj->{"bookurl"}:null;
				 $this->bookstatus = isset($obj->{"bookstatus"})?$obj->{"bookstatus"}:0;
				 $this->borrowname = isset($obj->{"borrow"}->{"borrowname"})?$obj->{"borrow"}->{"borrowname"}:null;
				 $this->borrowtime = isset($obj->{"borrow"}->{"borrowtime"})?$obj->{"borrow"}->{"borrowtime"}:0;
				 $this->borrowid = isset($obj->{"borrow"}->{"borrowid"})?$obj->{"borrow"}->{"borrowid"}:0;			 
				 				 
			 }
		 }catch (Exception $e) {
			throw new Exception($e->getMessage());
		}		 
	 }
	
    /**
     * @return mixed
     */
    public function getBookid()
    {
        return $this->bookid;
    }

    /**
     * @param mixed $bookid
     */
    public function setBookid($bookid)
    {
        $this->bookid = $bookid;
    }

    /**
     * @return mixed
     */
    public function getBookname()
    {
        return $this->bookname;
    }

    /**
     * @param mixed $bookname
     */
    public function setBookname($bookname)
    {
        $this->bookname = $bookname;
    }

	 /**
     * @return mixed
     */
    public function getBooknum()
    {
        return $this->booknum;
    }

    /**
     * @param mixed $booknum
     */
    public function setBooknum($booknum)
    {
        $this->booknum = $booknum;
    }
    /**
     * @return mixed
     */
    public function getBookauthor()
    {
        return $this->bookauthor;
    }

    /**
     * @param mixed $bookauthor
     */
    public function setBookauthor($bookauthor)
    {
        $this->bookauthor = $bookauthor;
    }

	 /**
     * @param mixed $bookurl
     */
    public function setBookurl($bookurl)
    {
        $this->bookurl = $bookurl;
    }

    /**
     * @return int
     */
    public function getBookurl()
    {
        return $this->bookurl;
    }
	
    /**
     * @return int
     */
    public function getBookstatus()
    {
        return $this->bookstatus;
    }

    /**
     * @param int $bookstatus
     */
    public function setBookstatus($bookstatus)
    {
        $this->bookstatus = $bookstatus;
    }

    /**
     * @return mixed
     */
    public function getBorrowid()
    {
        return $this->borrowid;
    }

    /**
     * @param mixed $borrowid
     */
    public function setBorrowid($borrowid)
    {
        $this->borrowid = $borrowid;
    }

    /**
     * @return mixed
     */
    public function getBorrowname()
    {
        return $this->borrowname;
    }

    /**
     * @param mixed $borrowname
     */
    public function setBorrowname($borrowname)
    {
        $this->borrowname = $borrowname;
    }

    /**
     * @return mixed
     */
    public function getBorrowtime()
    {
        return $this->borrowtime;
    }

    /**
     * @param mixed $borrowtime
     */
    public function setBorrowtime($borrowtime)
    {
        $this->borrowtime = $borrowtime;
    }

    /**
     * @return mixed
     */
    public function getReturntime()
    {
        return $this->returntime;
    }

    /**
     * @param mixed $returntime
     */
    public function setReturntime($returntime)
    {
        $this->returntime = $returntime;
    }


    public function addBook()
    {
            if($this->bookname){
                return   $this->insert("booklist",["booknum"=>$this->booknum,"bookname"=>$this->bookname,"bookurl"=>$this->bookurl,"bookauthor"=>$this->bookauthor]);
            }else{
                return false;
            }
    }
	
	/**
	* 原则上不删除数据库中数据。状态为-1代表此书已删除
	*/
    public function delBookByid(){
        if($this->bookid>0){
            //return $this->delete("booklist",["bookid"=>$this->bookid]);
			return $this->updateBookstatusByid($this->bookid,-1);
        }else{
            return false;
        }
    }
    /**
     * @return array|bool
     */
    public function getBooklist()
    {
        return $this->select("booklist","*",['bookstatus[>=]'=>0]);
    }

    /**
     * 根据书号查询最后的借阅者
     * @param $booknum
     * @return array|bool
     */
    public function getLastBorrowBynum(){
        if($this->booknum) {
            return $this->select("bookborrow", "*", ["AND" => ["booknum" => $this->booknum, "returntime[=]"=>0], "ORDER" => ["borrowtime" => "DESC"], "LIMIT" => 1]);
        }else{
            return false;
        }
    }
    /**
     * 根据bookid查询最后的借阅者
     * @param $booknum
     * @return array|bool
     */
    public function getLastBorrowByid($bookid){
        if($bookid) {
            return $this->select("bookborrow", "*", ["AND" => ["bookid" => $bookid, "returntime[=]"=>0], "ORDER" => ["borrowtime" => "DESC"], "LIMIT" => 1]);
        }else{
            return false;
        }
    }
    /**
     * @param $booklistarr
     * @return array
     */
    public function getBookAllList($booklistarr){
        $bookalllist = array();
        if(is_array($booklistarr) && $booklistarr){
            foreach ($booklistarr as $v){
                if($v["bookstatus"] == 1){
                    $arr = $this->getLastBorrowByid($v["bookid"]);

                    if($arr && $arr[0]){
                        $v["borrow"] = [
                            "borrowid"=>$arr[0]["borrowid"],
                            "borrowname"=>$arr[0]["borrowname"],
                            "borrowtime"=>$arr[0]["borrowtime"]
                        ];
                    }else{
                        //更改状态为0
                        if($this->updateBookstatusByid($v["bookid"],0))
                            $v["bookstatus"] = 0;
//                        $v["borrow"] = [
//                            "borrowname"=>"unkown",
//                            "borrowtime"=>0
//                        ];
                    }
                }
                $bookalllist[] = $v;
            }
        }
        return $bookalllist;
    }

    public function updateBookstatusByid($bookid,$bookstatus){
        return $this->update('booklist',['bookstatus'=>$bookstatus],['bookid'=>$bookid]);
    }

    public function insertBorrower(){
        return $this->insert('bookborrow',["borrowname"=>$this->borrowname,"borrowtime"=>$this->borrowtime,"bookid"=>$this->bookid,"booknum"=>$this->booknum]);
    }

    /**
     * @param $arr
     * @return int
     */
    public function returnBorrower(){
        return $this->update('bookborrow',["returntime"=>$this->returntime],["borrowid"=>$this->borrowid]);
    }

}