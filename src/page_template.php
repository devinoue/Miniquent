<?php
	

?>
	<!DOCTYPE html>
	<html lang="ja">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
		<title>Document</title>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	</head>
	<body>
		<nav aria-label="Page navigation example">
			<ul class="pagination">
			  <li class="page-item">
				<a class="page-link" href="#" aria-label="Previous">
				  <span aria-hidden="true">«</span>
				  <span class="sr-only">Previous</span>
				</a>
			  </li>
<?php

			 	// ここからがTemplateとして表示する箇所
	for($i=1;$i<=$this->page_length;$i++){
		if ($i == $this->active_page) {
			print<<<EOF
            <li class="page-item active">
                <a class="page-link" href="test.php?page=$i">
                $i<span class="sr-only">(current)</span>
                </a>
            </li>
EOF;
		}
		else {
			
			print<<<EOF
			<li class="page-item"><a class="page-link" href="test.php?page=$i">$i</a></li>
EOF;
		}
	}
?>
			  <li class="page-item">
				<a class="page-link" href="#" aria-label="Next">
				  <span aria-hidden="true">»</span>
				  <span class="sr-only">Next</span>
				</a>
			  </li>
			</ul>
		  </nav>
		  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
		  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	</body>
	</html>






	if ($this->total_num > 1){
	
	}