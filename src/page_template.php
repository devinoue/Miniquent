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
			<li class="page-item">
			<a class="page-link" href="test.php?page=$i">$i</a>
			</li>
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

