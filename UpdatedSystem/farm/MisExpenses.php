<html>

<head>
    <title>miscellaneous</title>
    <style>
        .form-label {
            text-align: left;
            display: block;
            /* Ensures it behaves like a block-level element */
        }

        .card {
            border: none;
            border-radius: 10px;
        }
    </style>
</head>

<body>
    <main class="col-lg-10 col-md-9 col-sm-8 p-0 vh-100 overflow-auto">
        <div class="container">
            <div class="row my-5 text-center">

                <div class="col-lg-5 col-md-10 col-12 mb-3 px-5">
                    <div class="card shadow">
                        <div class="card-header p-3 text-center" style="background-color: #356854;">
                            <h5 class="card-title text-white mb-0">
                                <strong style="font-size: 24px;">Add Miscellaneous Category</strong>
                            </h5>
                        </div>
                        <div class="card-body" style="background-color: #F5F5F5;"></div>
                        

                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">

                            <div class="mb-3">
                                <label for="staticEmail" class="form-control">category_name</label>
                                <input type="text" class="form-control" id="category_name"
                                    placeholder="e.g : transportation" name="category_name">
                            </div>
                            <!-- <div class="mb-3">
    <label for="inputPassword" class="form-control">category_description</label>
      <input type="text" class="form-control" id="category_description" name="category_description" placeholder="detailed description">
  </div> -->
                            <div class="mb-3">
                                <label for="category_description" class="form-control">category_description</label>
                                <input type="text" class="form-control" id="category_description"
                                    name="category_description" placeholder="detailed description">
                            </div>

                            <div class="d-grid gap-2">
                                <input type="submit" id="submit" value="submit">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>



</body>

</html>