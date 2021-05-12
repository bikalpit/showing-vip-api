<!DOCTYPE html>
<html lang="en">

<head>
    <title>Showing VIP - Forget Password</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap');

        body {
            font-family: 'Roboto';
            background-color: #FFF5EB;
        }
        body h4{
           width: 70%;
           display: block;
           margin: auto;
       margin-top: 95px;
       font-weight: 500;
        }

        #wm {
            background-color: #FFFFFF;
            box-shadow: 0px 3px 30px #0000000D;
            width: 100%;
            display: block;
            margin: auto;
            margin-top: 10px;
        }

        /* reset settings */
        * {
            padding: 0px;
            margin: 0px;
        }
         
        /* wm-section header-text */
        .wm-section1{
           padding-bottom: 60px;
        }
        .wm-header-text h3{
            text-align: center;
            color: #973C56;
            font-weight: 500;
            font-size: 18px;
            padding-top: 90px;
        }



        .wm-header h3 {
            text-align: center;
            margin-top: 10px;
            color: #000000;
            font-weight: 500;
            font-size: 22px;
            line-height: 1.2;
        }
    
    /* wm-header text & img & para */
        .wm-header img {
            display: block;
            margin-left: auto;
            margin-right: auto;
            width: 52px;
            height: 103px;
            margin-top: 50px;
        }

        .wm-header h3 {
            text-align: center;
            margin-top: 10px;
            color: #000000;
            font-weight: 500;
            font-size: 22px;
            line-height: 1.2;
        }

        .wm-header p {
            text-align: center;
            color: #973C56;
            margin-top: 15px;
            font-weight: 400;
        }

      

        /*wm-content*/
        .wm-content {
            margin-top: 20px;
            width: 61%;
            display: block;
            margin-left: auto;
            margin-right: auto;
            margin-top: 60px;
        }

        .wm-content p {
            color: #565657;
            margin-top: 33px;
        }

        .wm-content h3{
            margin-top: 20px;
            font-weight: 500;
      margin-bottom: 33px
        }

       
      



        ::marker {
            font-size: 30px;
            color: #973C56;
        }

        .wm-section-list p {
            text-align: center;
            color: #565657;
            font-weight: 500;
        }

      

        .wm-section-list .btn-more:focus {
            outline: none;
        }

        /*contact section*/
        .wm-section-contact {
            width: 70%;
            display: block;
            margin: auto;
      margin-top: -16px;
        }

        .wm-section-contact p {
            margin-top: 14%;
            margin-left: 50px;
            color: #565657;
        }

        .btn {
            width: 45%;
            float: left;
            margin-top: 40px;
        }

        .btn-dashboard {
            background-image: url(http://api.eventjio.com/email-template-image/greenbtn-bg.svg);
            background-size: cover;
            background-position: center;
            font-size: 15px;
            text-align: center;
            padding: 20px 60px;
            margin-left: 14%;
            color: #fff;
            border: none;
      background-color: transparent;
        }

        .btn-dashboard {
            outline: none;
        }
    
    

        .wm-section-contact h3 {
            margin-top: 8%;
            padding: 10px 50px;
            font-weight: 500;
        }

        /*wm-regards*/
        .wm-regards {
            width: 111%;
            display: block;
            margin: auto;
            margin-top: 104px;
            margin-bottom: 20px;
            background-color: #FFF5EB;
            box-shadow: 0px 3px 30px #0000000D;
            border-radius: 5px;
            opacity: 1;
        }

        .wm-regards p {
      padding: 49px 25px;
      font-weight: 500;
      padding-left: 8px;
        }
    #wm-regard1{
      margin-top: 14px;
    }
    #wm-cta{
      margin-top: 21px;
    }
    #text-color{
    color:blue;}
    
    
           
        /*wm-footer*/
        .wm-footer {
            background-color: #FFFFFF;
            box-shadow: 0px 3px 30px #0000000D;
            width: 70%;
            height: auto;
            display: block;
            margin: auto;
            margin-top: 3%;
            margin-bottom: 70px;
        }

        .wm-footer-text {
          text-align: center;
          padding: 20px;
        }

        .wm-footer-text h3 {
         margin-top: 10px;
         font-weight: 500;
        }
        .wm-footer-text p {
         margin-top: 15px;
         color: #565657;
        }
    </style>
</head>
	<body>
	    <div id="wm">
	        <div class="wm-section1">
	            <div class="wm-header-text">
	                <h3>Reset Password</h3>
	            </div>
	            <div class="wm-section-content" id="wm-content-head">
	                <div class="wm-content">
	                    <p>Hi {{$name}},</p><br/>
	          			<h3>Please click on below link to reset your password!</h3>
				        <a href="{{$app_url}}api/reset-password?token={{$verification_token}}" id="text-color">Reset Password</a>
	                </div>
	            </div>
	    	</div>
	  	</div>
	</body>
</html>