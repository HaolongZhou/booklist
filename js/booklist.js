
var app = angular.module('booklistApp', []);


app.controller('bookController', function($scope,$http,$window) {

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