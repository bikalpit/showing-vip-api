<!DOCTYPE html>
<html lang="en">

<head>
    <title>Showing VIP - Verify Email</title>
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
            /*background-image: url(http://api.eventjio.com/email-template-image/greenbtn-bg.svg);*/
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
            color:blue;
        }
           
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
	                <h3>Verify Email</h3>
	            </div>
	            <div class="wm-section-content" id="wm-content-head">
	                <div class="wm-content">
		                <p>Hi {{$name}},</p><br/>
		          		<h3>Your email verification OTP is {{$otp}}.</h3>
	                </div>
	            </div>
	    	</div>
	  	</div>
	</body>
</html>
<!-- ============== -->
<!DOCTYPE HTML
   PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"
   xmlns:o="urn:schemas-microsoft-com:office:office">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta name="x-apple-disable-message-reformatting">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <title></title>
      <style type="text/css">
         table,
         td {
         color: #000000;
         }
         a {
         color: #0000ee;
         text-decoration: underline;
         }
         @media only screen and (min-width: 620px) {
         .u-row {
         width: 600px !important;
         }
         .u-row .u-col {
         vertical-align: top;
         }
         .u-row .u-col-100 {
         width: 600px !important;
         }
         }
         @media (max-width: 620px) {
         .u-row-container {
         max-width: 100% !important;
         padding-left: 0px !important;
         padding-right: 0px !important;
         }
         .u-row .u-col {
         min-width: 320px !important;
         max-width: 100% !important;
         display: block !important;
         }
         .u-row {
         width: calc(100% - 40px) !important;
         }
         .u-col {
         width: 100% !important;
         }
         .u-col>div {
         margin: 0 auto;
         }
         }
         body {
         margin: 0;
         padding: 0;
         }
         table,
         tr,
         td {
         vertical-align: top;
         border-collapse: collapse;
         }
         p {
         margin: 0;
         }
         .ie-container table,
         .mso-container table {
         table-layout: fixed;
         }
         * {
         line-height: inherit;
         }
         a[x-apple-data-detectors='true'] {
         color: inherit !important;
         text-decoration: none !important;
         }
      </style>
      <link href="https://fonts.googleapis.com/css?family=Cabin:400,700&display=swap" rel="stylesheet" type="text/css">
   </head>
   <body class="clean-body"
      style="margin: 0;padding: 0;-webkit-text-size-adjust: 100%;background-color: #f9f9f9;color: #000000">
      <table
         style="border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;min-width: 320px;Margin: 0 auto;background-color: #f9f9f9;width:100%"
         cellpadding="0" cellspacing="0">
         <tbody>
            <tr style="vertical-align: top">
               <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
                  <div class="u-row-container" style="padding: 0px;background-color: transparent">
                     <div class="u-row"
                        style="Margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #ffffff;">
                        <div style="border-collapse: collapse;display: table;width: 100%;background-color: transparent;">
                           <div class="u-col u-col-100"
                              style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                              <div style="width: 100% !important;">
                                 <div
                                    style="padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;">
                                    <table style="font-family:'Cabin',sans-serif;" role="presentation" cellpadding="0" cellspacing="0"
                                       width="100%" border="0">
                                       <tbody>
                                          <tr>
                                             <td
                                                style="overflow-wrap:break-word;word-break:break-word;padding:20px;font-family:'Cabin',sans-serif;"
                                                align="left">
                                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                                   <tr>
                                                      <td style="padding-right: 0px;padding-left: 0px;" align="center">
                                                         <img align="center" border="0" src="{{$url}}public/mail-images/image-1.png" alt="Image" title="Image"
                                                            style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: inline-block !important;border: none;height: auto;float: none;width: 12%;max-width: 67.2px;"
                                                            width="67.2" />
                                                      </td>
                                                   </tr>
                                                </table>
                                             </td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>           
                  </div>
                  <div class="u-row-container" style="padding: 0px;background-color: transparent">
                     <div class="u-row"
                        style="Margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #e84c22;">
                        <div style="border-collapse: collapse;display: table;width: 100%;background-color: transparent;">
                           <div class="u-col u-col-100"
                              style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                              <div style="width: 100% !important;">
                                 <div
                                    style="padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;">
                                    <table style="font-family:'Cabin',sans-serif;" role="presentation" cellpadding="0" cellspacing="0"
                                       width="100%" border="0">
                                       <tbody>
                                          <tr>
                                             <td
                                                style="overflow-wrap:break-word;word-break:break-word;padding:40px 10px 10px;font-family:'Cabin',sans-serif;"
                                                align="left">
                                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                                   <tr>
                                                      <td style="padding-right: 0px;padding-left: 0px;" align="center">
                                                         <img align="center" border="0" src="{{$url}}public/mail-images/image-2.png" alt="Image" title="Image"
                                                            style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: inline-block !important;border: none;height: auto;float: none;width: 20%;max-width: 116px;"
                                                            width="116" />
                                                      </td>
                                                   </tr>
                                                </table>
                                             </td>
                                          </tr>
                                       </tbody>
                                    </table>
                                    <table style="font-family:'Cabin',sans-serif;" role="presentation" cellpadding="0" cellspacing="0"
                                       width="100%" border="0">
                                       <tbody>
                                          <tr>
                                             <td
                                                style="overflow-wrap:break-word;word-break:break-word;padding:0px 10px 31px;font-family:'Cabin',sans-serif;"
                                                align="left">
                                                <div
                                                   style="color: #e5eaf5; line-height: 140%; text-align: center; word-wrap: break-word;">
                                                   <p style="font-size: 14px; line-height: 140%;"><span
                                                      style="font-size: 28px; line-height: 39.2px;"><strong><span
                                                      style="line-height: 39.2px; font-size: 28px;">Verify your mail
                                                      </span></strong></span>
                                                   </p>
                                                </div>
                                             </td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="u-row-container" style="padding: 0px;background-color: transparent">
                     <div class="u-row"
                        style="Margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #ffffff;">
                        <div style="border-collapse: collapse;display: table;width: 100%;background-color: transparent;">
                           <div class="u-col u-col-100"
                              style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                              <div style="width: 100% !important;">
                                 <div
                                    style="padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;">
                                    <table style="font-family:'Cabin',sans-serif;" role="presentation" cellpadding="0" cellspacing="0"
                                       width="100%" border="0">
                                       <tbody>
                                          <tr>
                                             <td
                                                style="overflow-wrap:break-word;word-break:break-word;padding:33px 55px;font-family:'Cabin',sans-serif;"
                                                align="left">
                                                <div style="line-height: 160%; text-align: center; word-wrap: break-word;">
                                                   <p style="font-size: 14px; line-height: 160%;"><span
                                                      style="font-size: 22px; line-height: 35.2px;">Hi, {{$name}}</span></p>
                                                   <p style="font-size: 14px; line-height: 160%;"><span
                                                      style="font-size: 18px; line-height: 28.8px;">Your email verification OTP is {{$otp}} </span></p>
                                                </div>
                                             </td>
                                          </tr>
                                       </tbody>
                                    </table>
                                    <table style="font-family:'Cabin',sans-serif;" role="presentation" cellpadding="0" cellspacing="0"
                                       width="100%" border="0">
                                       <tbody>
                                          <tr>
                                             <td
                                                style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:'Cabin',sans-serif;"
                                                align="left">
                                                <div align="center">
                                                   <a style="font-size: 38px;
                                                      letter-spacing: 10px;
                                                      text-decoration: none;
                                                      color: #e84c22;
                                                      font-weight: 700;"> 1 4 2 9
                                                   </a>
                                                </div>
                                             </td>
                                          </tr>
                                       </tbody>
                                    </table>
                                    <table style="font-family:'Cabin',sans-serif;" role="presentation" cellpadding="0" cellspacing="0"
                                       width="100%" border="0">
                                       <tbody>
                                          <tr>
                                             <td
                                                style="overflow-wrap:break-word;word-break:break-word;padding:33px 55px 60px;font-family:'Cabin',sans-serif;"
                                                align="left">
                                                <div style="line-height: 160%; text-align: center; word-wrap: break-word;">
                                                   <p style="line-height: 160%; font-size: 14px;"><span
                                                      style="font-size: 18px; line-height: 28.8px;">Thanks,</span></p>
                                                   <p style="line-height: 160%; font-size: 14px;"><span
                                                      style="font-size: 18px; line-height: 28.8px;">The Showing VIP Team</span></p>
                                                </div>
                                             </td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="u-row-container" style="padding: 0px;background-color: transparent">
                     <div class="u-row"
                        style="Margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #e5eaf5;">
                        <div style="border-collapse: collapse;display: table;width: 100%;background-color: transparent;">
                           <div class="u-col u-col-100"
                              style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                              <div style="width: 100% !important;">
                                 <div
                                    style="padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;">
                                    <table style="font-family:'Cabin',sans-serif;" role="presentation" cellpadding="0" cellspacing="0"
                                       width="100%" border="0">
                                       <tbody>
                                          <tr>
                                             <td
                                                style="overflow-wrap:break-word;word-break:break-word;padding:41px 55px 18px;font-family:'Cabin',sans-serif;"
                                                align="left">
                                                <div
                                                   style="color: #003399; line-height: 160%; text-align: center; word-wrap: break-word;">
                                                   <p style="font-size: 14px; line-height: 160%;"><span
                                                      style="font-size: 20px; line-height: 32px;"><strong>Get in touch</strong></span></p>
                                                   <p style="font-size: 14px; line-height: 160%;"><span
                                                      style="font-size: 16px; line-height: 25.6px; color: #000000;">+11 111 333
                                                      4444</span>
                                                   </p>
                                                   <p style="font-size: 14px; line-height: 160%;"><span
                                                      style="font-size: 16px; line-height: 25.6px; color: #000000;">Info@showingvip.com</span>
                                                   </p>
                                                </div>
                                             </td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="u-row-container" style="padding: 0px;background-color: transparent">
                     <div class="u-row"
                        style="Margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #e84c22;">
                        <div style="border-collapse: collapse;display: table;width: 100%;background-color: transparent;">
                           <div class="u-col u-col-100"
                              style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                              <div style="width: 100% !important;">
                                 <div
                                    style="padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;">
                                    <table style="font-family:'Cabin',sans-serif;" role="presentation" cellpadding="0" cellspacing="0"
                                       width="100%" border="0">
                                       <tbody>
                                          <tr>
                                             <td
                                                style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:'Cabin',sans-serif;"
                                                align="left">
                                                <div
                                                   style="color: #fafafa; line-height: 180%; text-align: center; word-wrap: break-word;">
                                                   <p style="font-size: 14px; line-height: 180%;"><span
                                                      style="font-size: 16px; line-height: 28.8px;">Copyrights &copy; Company All Rights
                                                      Reserved</span>
                                                   </p>
                                                </div>
                                             </td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </td>
            </tr>
         </tbody>
      </table>
   </body>
</html>