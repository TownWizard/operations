<?php
function mailheader($var1){ 
	$headercode = '
							<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#f5f5f5"><tbody>
								<tr>
									<td>
										<table width="550" border="0" align="center" cellpadding="0" cellspacing="0"><tbody>
											<tr>
												<td width="161"><a href="http://www.townwizard.com" target="_blank"><img src="http://www.townwizard.com/wp-content/themes/5506/images/2012/header/townwizard_logo.png" alt="townwizard" width="262" height="84" border="0"></a></td>
												<td width="338" align="right"><p style="font:9px Helvetica Neue,Helvetica,Arial,sans-serif;letter-spacing:3px;margin:0;padding:0"><span style="color:#1a1a1a">'.$var1.'</span></p></td>
											</tr>
											<tr>
												<td height="50" colspan="2" >
													<table width="550" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff" style="border:1px double #e3e3e3;padding:0;margin:0"><tbody>
														<tr>
															<td width="40">&nbsp;</td>
															<td width="420" height="50"></td>
															<td width="40">&nbsp;</td>
														</tr>';
	return  $headercode;
}

function mailfooter(){
	$footercode = '
						</tbody></table>
						</td>
					</tr>
					<tr>
						<td height="10" align="center" valign="middle"  colspan="2" ></td>
					</tr>
					<tr>
						<td align="center" valign="middle" colspan="2"><p style="font:15px Helvetica Neue,Helvetica,Arial,sans-serif;font-weight:bold;margin:10px 0 10px 0;padding:10px 0 0 0;color:#000000;">OUR ADDRESS & SOCIAL MEDIA DETAILS</p></td>

					</tr>
					<tr>
						<td align="center" valign="middle" colspan="2" style="border-top:1px solid #e3e3e3">&nbsp;</td>
					</tr>
					<tr>
						<td height="20" align="center" colspan="2"><p style="font:12px Helvetica Neue,Helvetica,Arial,sans-serif;margin:0;padding:0;color:#777777;text-decoration:none">2, Overhill Road, Suite 400, Scarsdale, NY 10583 , USA</p></td>
					</tr>
					<tr>
						<td height="20" align="center" colspan="2">
						<p style="font:12px Helvetica Neue,Helvetica,Arial,sans-serif;margin:0;padding:0;color:#777777;text-decoration:none"><a style="font:12px Helvetica Neue,Helvetica,Arial,sans-serif;margin:0;padding:0;color:#777777;text-decoration:none" href="http://www.facebook.com/TownWizard"  target="_blank">Facebook &bull; </a><a style="font:12px Helvetica Neue,Helvetica,Arial,sans-serif;margin:0;padding:0;color:#777777;text-decoration:none" href="http://www.twitter.com/townwiz"  target="_blank">Twitter &bull; </a><a style="font:12px Helvetica Neue,Helvetica,Arial,sans-serif;margin:0;padding:0;color:#777777;text-decoration:none" href="http://www.linkedin.com/company/1592698?trk=tyah&amp;trkInfo=tas%3Atownwizard%20llc"  target="_blank">LinkedIn &bull; </a><a style="font:12px Helvetica Neue,Helvetica,Arial,sans-serif;margin:0;padding:0;color:#777777;text-decoration:none" href="http://www.youtube.com/channel/UCrwiyabEFIS0n0e87CB5nTg"  target="_blank">YouTube</a>
						</p>
						</td>
					</tr>
					<tr>
						<td height="50" align="center">&nbsp;</td>
					</tr>
		</tbody></table></td>
		</tr>
	</tbody></table>';
	return  $footercode;
}

?>