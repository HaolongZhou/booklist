
var app = angular.module('booklistApp', ['ngAnimate', 'mgcrea.ngStrap','mgcrea.ngStrap.tooltip','mgcrea.ngStrap.popover']);

app.config(function($popoverProvider) {
  angular.extend($popoverProvider.defaults, {
      animation: 'am-flip-x',
      html: true
  });
})
app.filter('encodeURL', function() {
    return function(text) {
        if(text) {
            return encodeURI(text);
        }
    }
});
app.controller('bookController', function($scope,$http,$modal,$popover,$window) {
  //$scope.togglePopover = function(book) {
	//popover-custom
	  $scope.popover = {title: '标题', content: '请输入姓名：',save:false};

	  var asAServiceOptions = {
		title: $scope.popover.title,
		content: $scope.popover.content,
    //    templateUrl: 'popover.demo.tpl.html',
		trigger: 'focus'
	  }



	  $scope.togglePopover = function(book) {
          var myPopover = $popover(document.getElementById('book'+book.bookid), asAServiceOptions);
		myPopover.$promise.then(myPopover.toggle);
	  };

    $scope.borrowBooks = [];
    $scope.shelfBooks = [];
    
    var updateBooks = function() {
        $scope.borrowBooks = [];
        $scope.shelfBooks = [];
		
        $scope.books.map(function(b) {			
            if(b.bookstatus == 1){
                $scope.borrowBooks.push(b);
            }else{
                $scope.shelfBooks.push(b);
			}
        });

    };
	
	var addBooksDB = function($d,$fn) {
        $http.post('server/book.php?a=addbook',
            JSON.stringify($d)
        ).then(
            function(result) {
                if(result.data.bookid > 0){
                    //   alert(result.data+"增加成功"+result);
                    $scope.books.map(function (b) {
                        if(b.booknum == $d.booknum){
                            b.bookid = result.data.bookid;
                        }
                    })

                    return $fn;
                }else{
                    alert(result.data.bookid+"：增加失败");
                };
            }
        )
    }
    var delBooksDB = function($d,$fn) {
        $http.post('server/book.php?a=delbook',
            JSON.stringify($d)
        ).then(
            function(result) {
                if(result.data.res){
                    //alert(result.data.res+"删除成功");
                    return $fn;
                }else{
                    alert(result.data.bookid+"：删除失败");
                    return false;
                };
            }
        ).catch(function (result) {
            alert(result.message);
        })
    }
	var addBorrowerDB = function ($d,$fn) {
        $http.post(
            'server/book.php?a=addborrow',
            JSON.stringify($d)
        ).then(
            function (result) {

                if(result.data.borrowid > 0){
                    $scope.books.map(function (b) {
                        if(b.bookid == $d.bookid){
                            b.borrow.borrowid = result.data.borrowid;
                        }
                    })

                    alert(result.data.borrowid+"：借阅登记成功");
                    return $fn;
                }else{
                    alert(result.data.borrowid+"：借阅登记失败");
                }
            }
        );
    }

    var removeBorrowerDB = function ($d,$fn) {
        $http.post(
            'server/book.php?a=removeborrow',
            JSON.stringify($d)
        ).then(function (result) {
                if (result.data.res > 0) {
                    alert(result.data.res + "：还书登记成功");
                    return $fn;
                } else {
                    alert(result.data.res + "：还书登记失败");
                }
            }
        ).catch (function (result){
            alert(result.message);
        });
    }

    //增加新书
    $scope.addBook = function() {
        var book = {};		
		var timestamp=new Date().getTime();

        if($scope.newBookName) {
			book.booknum=timestamp;            
            book.bookname = $scope.newBookName;
			book.bookauthor = $scope.newBookAuthor;
			book.bookurl = $scope.newBookURL;
            book.borrow = false;
            book.info = false;
            $scope.books.push(book);
            $scope.newBookAuthor = '';
            $scope.newBookName = '';
        }
		addBooksDB(book,updateBooks());
      //  updateBooks();
    };

    //还书
    $scope.shelfBook = function(book) {
        $scope.books.map(function(b) {
            if(b.bookid == book.bookid) {
                var timestamp=new Date().getTime();
                b.bookstatus = 0;
                b.borrow.returntime = timestamp;
                removeBorrowerDB(b,updateBooks());
            }
        });

    }

    //借书
    $scope.borrowBook = function(book) {
		var timestamp=new Date().getTime();
        $scope.books.map(function(b) {
            if(b.bookid == book.bookid) {
                if(typeof(book.borrow) != "undefined" && book.borrow.borrowname.length > 1) {
                    b.bookstatus = 1;
                    b.borrow.borrowtime = timestamp;
                    //b.borrow = {borrowname:"李四",borrowtime:timestamp};
                    addBorrowerDB(b, updateBooks());
                }
            }
        });
    };

    //删除图书
    $scope.removeBook = function(book) {
        if($window.confirm('确定要删除 《'+book.bookname+'》 吗?')) {
            $scope.books.filter(function(b) {
                if(b.bookid == book.bookid){
                    //alert($scope.books.indexOf(b)+b.bookname);
                    var i = $scope.books.indexOf(b);
                    delBooksDB(b, $scope.books.splice(i,1));
                }
            });
            updateBooks();
        } else {

        }

    };



    $scope.infoBook = function(book) {
        book.info = !book.info;
        updateBooks();
    };

    $scope.books = [];	

	$http.get('server/book.php?a=booklist')
		.then(function (result) {$scope.books = result.data;updateBooks();})
		.catch(function (result) { //捕捉错误处理  
			console.info(result);  
			alert(result.Message);  
		});
		
	
	
});