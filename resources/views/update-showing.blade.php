<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<div class="jumbotron text-center">
  <h1 class="display-3">Thank You!</h1>
  @if ($status == 'accept')
  	<p class="lead"><strong>Showing has been approved.</strong></p>
  @elseif ($status == 'reject')
  	<p class="lead"><strong>Showing has been rejected.</strong></p>
  @endif
  <hr>
</div>