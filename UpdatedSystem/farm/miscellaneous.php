<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_name=$_POST[""];
}
?>
<html>
    <head><title>miscellaneous</title></head>
    <body>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
  <div class="form-group row">
    <label for="staticEmail" class="col-sm-2 col-form-label">category_name</label>
    <div class="col-sm-10"> 
      <input type="text" readonly class="form-control-plaintext" id="category_name" name="category_name" value="Transportation">
    </div>
  </div>
  <div class="form-group row">
    <label for="inputPassword" class="col-sm-2 col-form-label">category_description</label>
    <div class="col-sm-10">
      <input type="text" class="description" id="category_description" id="category_description" placeholder="detailed description">
    </div>
  </div>
  
  <input type="submit"  id="submit" value="submit">

</form>
</body>
</html>


