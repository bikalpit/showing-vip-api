<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
@if($status == 'verified')
	<div class="jumbotron text-center">
	  <h1 class="display-3">Thank You!</h1>
	  <p class="lead"><strong>Your property verified successfully.</strong></p>
	  <hr>
	  <!-- <p>
	    Having trouble? <a href="">Contact us</a>
	  </p>
	  <p class="lead">
	    <a class="btn btn-primary btn-sm" href="https://bootstrapcreative.com/" role="button">Continue to homepage</a>
	  </p> -->
	</div>
@else
	<div class="jumbotron text-center">
	  <h3 class="display-3">Sorry, Verification link is exipered or invalid!</h3>
	</div>
@endif