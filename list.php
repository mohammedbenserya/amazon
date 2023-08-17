<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>Amazon Category Scraper</title>
</head>
<body>

    <div class="container mt-5 pt-5 w-50" >
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item ">
        <a class="nav-link" href="single.php">Single Product  </a>
      </li>
      <li class="nav-item active">
        <a class="nav-link" href="list.php">List of Products (Category)</a></a>
      </li>
      
    </ul>
  </div>
</nav>
    <div class="alert alert-primary" id='alert' style="display: none" role="alert">
  </div>
  
        <form action="category.php" id='form' method="get">
            <input type="" hidden name="ccheck" value="ccheck">
            <div class="mb-3">
              <label for="exampleInputEmail1" class="form-label">Category's Link :</label>
              <input type="url" class="form-control" id="exampleInputEmail1" name="url" aria-describedby="emailHelp">
            </div>
           
            <div class="alert alert-success w-50" id='file' style="display: none" role="alert">
            <a href="product.php?path=products.json" id="download" class="btn btn-outline-success">Success</a>
          </div>
            <button type="submit" id ='btn' class="btn btn-primary" onsubmit="$('#form').submit()">Submit</button>
            <div class="spinner-border text-primary" id='load' role="status" style="display: none">
  <span class="sr-only" ></span>
</div>
          </form>
          <p></p>

    </div>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script type="text/javascript">

$(document).ready(function () {

  $('#form').submit(function (e) {
  $('#btn').hide(); 
  $('#load').show(); 

e.preventDefault();
var senddata = $(this).serializeArray();
var sendto = $(this).attr("action");
$.ajax({
    url: sendto,
    type: 'POST',
    data: senddata,
    success: function (data) {
        data = parseInt(data);
        console.log(data);

        $('#alert').html('There ' + (data < 1 ? 'is' : 'are') + ' ' + data + ' pages to scrape').show().delay(2000).fadeOut(600);

        const categoryPromises = [];

        for (let i = 1; i <= data; i++) {
            const promise = new Promise((resolve, reject) => {
                $.ajax({
                    url: 'category.php',
                    type: 'POST',
                    data: { "url": $('input[name=url]').val(), "page": i },
                    success: function (res) {
                        const categoryData = JSON.parse(res);
                        const productPromises = categoryData.map(function (v) {
                            return new Promise((resolveProduct, rejectProduct) => {
                                $.ajax({
                                    url: 'product.php',
                                    type: 'POST',
                                    data: { "asin": v[0], 'iscat': 'true' },
                                    success: function (response) {
                                        console.log(response);
                                        resolveProduct(response);
                                    },
                                    error: function (error) {
                                        console.log("Error processing product: " + error);
                                        rejectProduct(error);
                                    }
                                });
                            });
                        });

                        Promise.all(productPromises)
                            .then(products => {
                                console.log("Products for category " + i + " processed.");
                                resolve(products);
                            })
                            .catch(error => {
                                console.log("Error processing products for category " + i + ": " + error);
                                reject(error);
                            });
                    },
                    error: function (error) {
                        console.log("Error processing category: " + error);
                        reject(error);
                    }
                });
            });
            console.log(promise);
            categoryPromises.push(promise);
        }

        Promise.all(categoryPromises)
            .then((responses) => {
                const allResponses = responses.flat(); // Flatten the array of responses
                
                // Convert allResponses to JSON string
                const jsonData = JSON.stringify(allResponses);
                
                // Create a Blob containing the JSON data
                const blob = new Blob([jsonData], { type: 'application/json' });

                // Create a download link
                const downloadLink = document.createElement('a');
                downloadLink.href = URL.createObjectURL(blob);
                downloadLink.download = 'scraped_data.json';
                downloadLink.click();
                $('#load').hide(); 
                $('#asin').val(''); // Clear the input
                $('#btn').show(); 
       
//$('#file').show(); 
            })
            .catch(error => {
                console.log("Error: " + error);
            });
    },
    error: function (error) {
        $('.messages').html(error);
    }
});

        //
}); 
$('#download').click(function (e) {
      // Allow the download link to follow the link first (download the file)
      // and then execute the code inside the click handler
      setTimeout(function () {
        $('#file').hide(); // Hide the parent div of the download link
        $('#asin').val(''); // Clear the input
        $('#btn').show(); // Show the submit button
      }, 100); // You can adjust the delay if needed
    });
  });
</script>
</html>