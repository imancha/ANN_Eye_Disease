<!--
 * index.php
 * 
 * Copyright 2014 Imancha <imancha_266@ymail.com>
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * 
 -->
<?php
	/*	Connect Database	*/
	function mysql_open(){
		$host = "localhost";
		$username = "root";
		$password = "root";
		$database = "ANN";
	
		mysql_connect($host, $username, $password) or die(mysql_error());
		mysql_select_db($database);
	}
	/*	Header	*/
	function head(){
		echo '<h2>Neural Networks For Eye Diseases Diagnosis</h2>';
	}
	/*	Navigation	*/
	function navigation(){
		echo '<ul>
						<li><a href="?!=home">Home</a></li>
						<li><a href="?!=disease">Eye Disease</a></li>
					</ul>';
	}
	/*	Footer	*/
	function footer(){
		echo 'Copyright &copy; 2014 - UNIKOM';
	}	
	/*	Sum Percentage	*/
	function percent($a, $b){
		foreach($a as $va){
			foreach($b as $vb){
				if($va == $vb){
					$sum++;
				}
			}
		}
		return round(($sum*(100/count($b))),0);
	}
	/*	Sort Descending	*/
	function bsort($a){
		for($i=0; $i<count($a)-1; $i++){
			for($j=0; $j<=count($a)-$i; $j++){
				if($a[$j]['percent'] < $a[$j+1]['percent']){
					$t = $a[$j];
					$a[$j] = $a[$j+1];
					$a[$j+1] = $t;
				}
			}
		}
		return $a;
	}
	/*	Matching an element array	*/
	function match($a, $b){
		foreach($a as $va){
			if($va == $b){
				return true;
				break;
			}
		}
		return false;
	}

	/*	Action	*/
	function main(){
		switch($_GET['!']){
			case 'submit' :
				mysql_open();
			
				/*	Get the name of Symptoms from Database	*/
				$sql = "SELECT name FROM `Symptoms` WHERE id IN (".implode(', ', $_POST['symptoms']).")";
				$res = mysql_query($sql) or die(header('Location: '.$_SERVER['HTTP_REFERER']));
			
				if(mysql_num_rows($res) > 0){
					while($row = mysql_fetch_array($res)){
						/*	Store the name of Symptoms to the Array	*/
						$signs[] = $row['name'];
					}
					
					/*	Formatting the name of Symptoms from Array	*/
					$asigns = '<ul class="list-unstyled">';
					foreach($signs as $val){
						$asigns .= '<li>'.$val.'</li>';
					}
					$asigns .= '</ul>';
				
					/*	Get the name of Disease from Database	*/
					$sql = "SELECT * FROM `Eye Disease`";
					$res = mysql_query($sql) or die(mysql_error());
				
					if(mysql_num_rows($res) > 0){
						while($row = mysql_fetch_array($res)){							
							/*	Get the name of Symptoms of Disease from database	*/
							$sql0 = "SELECT name FROM `Symptoms` WHERE id IN (".implode(', ', explode(' ', $row['signs'])).")";
							$res0 = mysql_query($sql0) or die(mysql_error());
						
							if(mysql_num_rows($res0) > 0){
								/*	Formatting the name of Symptoms from database	*/
								$sign = '<ul class="list-unstyled">';
								while($row0 = mysql_fetch_array($res0)){
									$sign .= '<li>'.$row0['name'].'</li>';
								}
								$sign .= '</ul>';
					
								/*	Store the result to the Array	*/
								$diseases[] = array('name' => $row['name'],
																		'sign' => $sign, 
																		'percent' => percent($_POST['symptoms'], explode(' ', $row['signs']))
																	 );
							}							
						}

						/*	Sort the array of symptoms to Descending	*/
						$disease = bsort($diseases);
						
						/*	Display the Other Result of Diseases	*/
						$result .= '<table class="table table-striped">
												 	<tbody>';
						for($i=1; $i<count($disease); $i++){
							$result .= '	<tr>
															<th>Signs</th>
															<th>'.$disease[$i]['name'].'</th>
															<th style="text-align: right; padding-right: 12px;">(%)</th>
														</tr>
														<tr>
															<td>'.$asigns.'</td>																
															<td>'.$disease[$i]['sign'].'</td>
															<td style="vertical-align: middle; text-align: center; font-weight: bold; font-size: 27px;">'.$disease[$i]['percent'].'</td>
														</tr>';
						}
						$result .= '	</tbody>
												</table>';
						
						$sql1 = "SELECT * FROM `Symptoms`";
						$res1 = mysql_query($sql1) or die(mysql_error());

						$sql2 = "SELECT * FROM `Eye Disease`";
						$res2 = mysql_query($sql2) or die(mysql_error());

						$input = mysql_num_rows($res1);
						$hidden = 9;
						$output = mysql_num_rows($res2);
						$threshold = 1;
						$a = 0.1;
						$e = 2.7182818285;
						$t = -1;
						$etarget = 0.02;
						$epoch = 0;						

						/*	Random initial weight input to hidden	*/
						for($i=0; $i<$input; $i++){
							for($j=0; $j<$hidden; $j++){
								/*	Random between -0.9 and 0.9	*/
								$V[$i][$j][$epoch] = round(rand()/getrandmax()*1.8-0.9, 1);
							}
						}
						/*	Random initial weight threshold to hidden	*/						
						for($i=0; $i<$hidden; $i++){
							/*	Random between -0.9 and 0.9	*/
							$th[$i][$epoch] = round(rand()/getrandmax()*1.8-0.9, 1);
						}
						/*	Random initial weight hidden to output	*/
						for($i=0; $i<$hidden; $i++){
							for($j=0; $j<$output; $j++){
								/*	Random between -0.9 and 0.9	*/
								$W[$i][$j][$epoch] = round(rand()/getrandmax()*1.8-0.9, 1);
							}
						}
						/*	Random initial weight threshold to output	*/						
						for($i=0; $i<$output; $i++){
							/*	Random between -0.9 and 0.9	*/
							$to[$i][$epoch] = round(rand()/getrandmax()*1.8-0.9, 1);
						}
						/*	Value of input	*/				
						while($row1 = mysql_fetch_array($res1)){															
							if(match($_POST['symptoms'], $row1['id'])){
								/*	If sign is checked	*/
								$ai[] = 1;
							}else{
								$ai[] = 0;
							}
						}
						/*	Value of output, hard limit activation function	*/
						for($i=0; $i<count($diseases); $i++){							
							if($diseases[$i]['percent'] > 0){
								$Y[][$epoch] = 1;
							}else{
								/*	If percentage less equal than 0	*/
								$Y[][$epoch] = 0;
							}
						}
						/*	Activation	Function	*/
						do{							
							$epoch++;
							/*	Actual output hidden layer	*/
							for($i=0; $i<$hidden; $i++){
								$ee = 0;
								for($j=0; $j<$input; $j++){
									$ee += $ai[$j]*$V[$j][$i][$epoch-1];
								}
								$Z[$i][$epoch] = round(1/(1+(pow($e, -($ee-$th[$i][$epoch-1])))),4);								
							}
							/*	Actual output output layer	*/
							for($i=0; $i<$output; $i++){
								$ee = 0;
								for($j=0; $j<$hidden; $j++){
									$ee += $Z[$j][$epoch]*$W[$j][$i][$epoch-1];
								}
								$Y[$i][$epoch] = round(1/(1+(pow($e, -($ee-$to[$i][$epoch-1])))),4);
							}
							/*	Error	*/
							for($i=0; $i<$output; $i++){
								$error[$i][$epoch] = $Y[$i][0] - $Y[$i][$epoch];
							}
							/*	Error gradient output layer	*/
							for($i=0; $i<$output; $i++){
								$errno[$i][$epoch] = round($Y[$i][$epoch]*(1-$Y[$i][$epoch])*$error[$i][$epoch],4);
							}
							/*	Weight correction output layer	*/
							for($i=0; $i<$hidden; $i++){
								for($j=0; $j<$output; $j++){								
									$DW[$i][$j][$epoch] = round($a*$Z[$i][$epoch]*$errno[$j][$epoch],4);
								}
							}
							/*	Weight correction threshold output layer	*/
							for($i=0; $i<$output; $i++){
								$Dto[$i][$epoch] = round($a*$t*$errno[$i][$epoch],4);
							}
							/*	Error gradient hidden layer	*/
							for($i=0; $i<$hidden; $i++){
								$er = 0;
								for($j=0; $j<$output; $j++){
									$er += $errno[$j][$epoch]*$W[$i][$j][$epoch-1];
								}
								$errnh[$i][$epoch] = round($Z[$i][$epoch]*(1-$Z[$i][$epoch])*$er,4);
							}
							/*	Weight correction hidden layer	*/
							for($i=0; $i<$input; $i++){
								for($j=0; $j<$hidden; $j++){
									$DV[$i][$j][$epoch] = round($a*$ai[$i]*$errnh[$j][$epoch],4);
								}
							}
							/*	Weight correction threshold hidden layer	*/
							for($i=0; $i<$hidden; $i++){
								$Dth[$i][$epoch] = round($a*$t*$errnh[$i][$epoch],4);
							}
							/*	Update weight input to hidden	*/
							for($i=0; $i<$input; $i++){
								for($j=0; $j<$hidden; $j++){
									$V[$i][$j][$epoch] = $V[$i][$j][$epoch-1]+$DV[$i][$j][$epoch];
								}
							}
							/*	Update weight threshold to hidden	*/						
							for($i=0; $i<$hidden; $i++){								
								$th[$i][$epoch] = $th[$i][$epoch-1]+$Dth[$i][$epoch];
							}
							/*	Update weight hidden to output	*/
							for($i=0; $i<$hidden; $i++){
								for($j=0; $j<$output; $j++){									
									$W[$i][$j][$epoch] = $W[$i][$j][$epoch-1]+$DW[$i][$j][$epoch];
								}
							}
							/*	Update weight threshold to output	*/						
							for($i=0; $i<$output; $i++){								
								$to[$i][$epoch] = $to[$i][$epoch-1]+$Dto[$i][$epoch];
							}
							/*	Sum of squared error	*/
							for($i=0; $i<$output; $i++){
								$serror[$epoch] += pow($error[$i][$epoch],2);
							}
						}while(($serror[$epoch] > $etarget));

						/*	Display Inputs table	*/
						$inputs .= '<table class="table table-bordered table-hover">
													<thead style="background: #F9F9F9;">
														<tr>
															<th colspan="'.$input.'" style="text-align:center;">Inputs</th>
														</tr>
														<tr>';
														for($i=0; $i<$input; $i++){
															$inputs .= '<th style=" text-align:center;">x<sub>'.($i+1).'</sub></th>';
														}
						$inputs .= '		</tr>
													</thead>
													<tbody>
														<tr>';														
														foreach($ai as $vai){
															$inputs .= '<td style="text-align:center;">'.$vai.'</td>';
														}
						$inputs .= '		</tr>
													</tbody>
												</table>';

						/*	Display Desired Outputs table	*/
						$doutputs .= '<table class="table table-bordered table-hover">
													<thead style="background: #F9F9F9;">
														<tr>
															<th colspan="'.$output.'" style="text-align:center;">Desired Outputs</th>
														</tr>
														<tr>';
														for($i=0; $i<$output; $i++){
															$doutputs .= '<th style="text-align:center;">y<sub>'.($i+1).'</sub></th>';
														}
						$doutputs .= '	</tr>
													</thead>
													<tbody>
														<tr>';														
														foreach($Y as $vY){
															$doutputs .= '<td style="text-align:center;">'.$vY[0].'</td>';
														}
						$doutputs .= '		</tr>
													</tbody>
												</table>';

						/*	Display init table	*/
						$init .= '<table class="table table-bordered table-hover">
												<thead style="background: #F9F9F9;">
													<tr>
														<th colspan="4" style="text-align: center;">Sum of Neuron</th>
														<th rowspan="2" style="text-align: center; vertical-align: middle;">Learning Rate</th>
														<th rowspan="2" style="text-align: center; vertical-align: middle;">Sum of Squared Error</th>														
													</tr>
													<tr>
														<th style="text-align: center;">Input Layer</th>
														<th style="text-align: center;">Hidden Layer</th>
														<th style="text-align: center;">Output Layer</th>
														<th style="text-align: center;">Threshold Layer</th>
													</tr>
												</thead>
												<tbody style="text-align: center;">
													<tr>
														<td>'.$input.'</td>
														<td>'.$hidden.'</td>
														<td>'.$output.'</td>
														<td>'.$threshold.'</td>
														<td>'.$a.'</td>
														<td>'.$etarget.'</td>														
													</tr>
												</tbody>
											 </table>';

						/*	Display initial weight input to hidden table	*/
						$initxz .= '<table class="table table-bordered table-hover">
													<thead style="background: #F9F9F9;">
														<tr>
															<th colspan="'.($hidden+1).'" style="text-align: center;">Initial weight input layer to hidden layer</th>
														</tr>
														<tr>
															<th style="text-align: center; border-right: 2px solid #DDDDDD;">#</th>';
															for($i=0; $i<$hidden; $i++){
																$initxz .= '	<th style="text-align: center;">z<sub>'.($i+1).'</sub></th>';
															}
						$initxz .= '		</tr>
													</thead>
													<tbody>';
														for($i=0; $i<$input; $i++){
															$initxz .= '<tr>																														
																						<th style="background: #F9F9F9; text-align: center; border-right: 2px solid #DDDDDD;">x<sub>'.($i+1).'</sub></th>';
															for($j=0; $j<$hidden; $j++){
																$initxz .= '	<td style="text-align: center;">'.$V[$i][$j][0].'</td>';
															}
															$initxz .= '</tr>';
														}
						$initxz .= '		<tr>
															<th style="background: #F9F9F9; text-align: center; border-right: 2px solid #DDDDDD;">θ</th>';
															for($i=0; $i<$hidden; $i++){
																$initxz .= '<td style="text-align: center;">'.$th[$i][0].'</td>';
															}
						$initxz .= '		</tr>
													</tbody>
												</table>';

						/*	Display initial weight hidden to output table	*/
						$initzy .= '<table class="table table-bordered table-hover">
													<thead style="background: #F9F9F9;">
														<tr>
															<th colspan="'.($output+1).'" style="text-align: center;">Initial weight hidden layer to output layer</th>
														</tr>
														<tr>
															<th style="text-align: center; border-right: 2px solid #DDDDDD;">#</th>';
															for($i=0; $i<$output; $i++){
																$initzy .= '<th style="text-align: center;">y<sub>'.($i+1).'</sub></th>';
															}
						$initzy .= '		</tr>
													</thead>
													<tbody>';
														for($i=0; $i<$hidden; $i++){
															$initzy .= '<tr>																														
																						<th style="background: #F9F9F9; text-align: center; border-right: 2px solid #DDDDDD;">z<sub>'.($i+1).'</sub></th>';
															for($j=0; $j<$output; $j++){
																$initzy .= '<td style="text-align: center;">'.$W[$i][$j][0].'</td>';
															}
															$initzy .= '</tr>';
														}
						$initzy .= '		<tr>
															<th style="background: #F9F9F9; text-align: center; border-right: 2px solid #DDDDDD;">θ</th>';
															for($i=0; $i<$output; $i++){
																$initzy .= '<td style="text-align: center;">'.$to[$i][0].'</td>';
															}
						$initzy .= '		</tr>
													</tbody>
												</table>';
						
						/*	Display initialisation panel	*/
						$initialisation .= '<div class="panel panel-default">
																	<div class="panel-heading">												 			
																		<h4 class="panel-title">												 				
																			<a data-toggle="collapse" data-parent="#bccordion" href="#initialisation" title="Initialisation">Initialisation</a>
																		</h4>
																	</div>
																	<div id="initialisation" class="panel-collapse collapse">
																		<div class="panel-body">
																			'.$initxz.'
																			'.$initzy.'
																		</div>
																	</div>
																</div>';

						/*	Display activation table	*/
						$aut .= '<table class="table table-striped">
											<tbody>
												<tr>
													<th colspan="'.$hidden.'">Epoch 0</th>
												</tr>
												<tr>
													<td>
														<table class="table table-bordered table-hover">
															<thead style="background: #F9F9F9;">
																<tr>																	
																	<th colspan="'.$hidden.'" style="text-align:center;">Actual Outputs</th>
																</tr>
																<tr>';
																	for($j=0; $j<$hidden; $j++){
																		$aut .= '<th style="text-align:center;">z<sub>'.($j+1).'</sub></th>';
																	}
						$aut .= '						</tr>
															</thead>
															<tbody>
																<tr>';
																	for($j=0; $j<$hidden; $j++){
																		$aut .= '<td style="text-align:center;">'.$Z[$j][1].'</td>';
																	}
						$aut .= '						</tr>
															</tbody>															
														</table>
														<table class="table table-bordered table-hover">
															<thead style="background: #F9F9F9;">
																<tr>																	
																	<th colspan="'.$output.'" style="text-align:center;">Actual Outputs</th>
																</tr>
																<tr>';
																	for($j=0; $j<$output; $j++){
																		$aut .= '<th style="text-align:center;">y<sub>'.($j+1).'</sub></th>';
																	}
						$aut .= '						</tr>
															</thead>
															<tbody>
																<tr>';
																	for($j=0; $j<$output; $j++){
																		$aut .= '<td style="text-align:center;">'.$Y[$j][1].'</td>';
																	}
						$aut .= '						</tr>
															</tbody>															
														</table>
														<table class="table table-bordered table-hover">
															<thead style="background: #F9F9F9;">
																<tr>																	
																	<th colspan="'.$output.'" style="text-align:center;">Error</th>
																	<th rowspan="2" style="text-align: center; vertical-align: middle;">Sum of Squared Error</th>
																</tr>
																<tr>';
																	for($j=0; $j<$output; $j++){
																		$aut .= '<th style="text-align:center;">e<sub>'.($j+1).'</sub></th>';
																	}
						$aut .= '						</tr>
															</thead>
															<tbody>
																<tr>';
																	for($j=0; $j<$output; $j++){
																		$aut .= '<td style="text-align:center;">'.$error[$j][1].'</td>';
																	}
						$aut .= '							<td style="text-align: center;">'.(round($serror[1],8)).'</td>
																</tr>
															</tbody>															
														</table>
														<table class="table table-bordered table-hover">
															<thead style="background: #F9F9F9;">
																<tr>																	
																	<th colspan="'.$output.'" style="text-align:center;">Error Gradient</th>
																</tr>
																<tr>';
																	for($j=0; $j<$output; $j++){
																		$aut .= '<th style="text-align:center;">y<sub>'.($j+1).'</sub></th>';
																	}
						$aut .= '						</tr>
															</thead>
															<tbody>
																<tr>';
																	for($j=0; $j<$output; $j++){
																		$aut .= '<td style="text-align:center;">'.$errno[$j][1].'</td>';
																	}
						$aut .= '						</tr>
															</tbody>															
														</table>
														<table class="table table-bordered table-hover">
															<thead style="background: #F9F9F9;">
																<tr>																	
																	<th colspan="'.($output+1).'" style="text-align:center;">Weight Correction</th>
																<tr>
																	<th style="text-align: center; border-right: 2px solid #DDDDDD;">#</th>';
																	for($j=0; $j<$output; $j++){
																		$aut .= '<th style="text-align: center;">y<sub>'.($j+1).'</sub></th>';
																	}												
						$aut .= '						</tr>
															</thead>
															<tbody>';
																for($j=0; $j<$hidden; $j++){
																	$aut .= '<tr>																														
																						<th style="background: #F9F9F9; text-align: center; border-right: 2px solid #DDDDDD;">z<sub>'.($j+1).'</sub></th>';
																	for($k=0; $k<$output; $k++){
																		$aut .= '<td style="text-align: center;">'.$DW[$j][$k][1].'</td>';
																	}
																	$aut .= '</tr>';
																}							
						$aut .= '						<tr>
																	<th style="background: #F9F9F9; text-align: center; border-right: 2px solid #DDDDDD;">θ</th>';
																	for($j=0; $j<$output; $j++){
																		$aut .= '<td style="text-align: center;">'.$Dto[$j][1].'</td>';
																	}
						$aut .= '						</tr>
															</tbody>															
														</table>
														<table class="table table-bordered table-hover">
															<thead style="background: #F9F9F9;">
																<tr>																	
																	<th colspan="'.$hidden.'" style="text-align:center;">Error Gradient</th>
																</tr>
																<tr>';
																	for($j=0; $j<$hidden; $j++){
																		$aut .= '<th style="text-align:center;">z<sub>'.($j+1).'</sub></th>';
																	}
						$aut .= '						</tr>
															</thead>
															<tbody>
																<tr>';
																	for($j=0; $j<$hidden; $j++){
																		$aut .= '<td style="text-align:center;">'.$errnh[$j][1].'</td>';
																	}
						$aut .= '						</tr>
															</tbody>															
														</table>
														<table class="table table-bordered table-hover">
															<thead style="background: #F9F9F9;">
																<tr>																	
																	<th colspan="'.($hidden+1).'" style="text-align:center;">Weight Correction</th>
																<tr>
																	<th style="text-align: center; border-right: 2px solid #DDDDDD;">#</th>';
																	for($j=0; $j<$hidden; $j++){
																		$aut .= '<th style="text-align: center;">z<sub>'.($j+1).'</sub></th>';
																	}												
						$aut .= '						</tr>
															</thead>
															<tbody>';
																for($j=0; $j<$input; $j++){
																	$aut .= '<tr>																														
																						<th style="background: #F9F9F9; text-align: center; border-right: 2px solid #DDDDDD;">x<sub>'.($j+1).'</sub></th>';
																	for($k=0; $k<$hidden; $k++){
																		$aut .= '<td style="text-align: center;">'.$DV[$j][$k][1].'</td>';
																	}
																	$aut .= '</tr>';
																}							
						$aut .= '						<tr>
																	<th style="background: #F9F9F9; text-align: center; border-right: 2px solid #DDDDDD;">θ</th>';
																	for($j=0; $j<$hidden; $j++){
																		$aut .= '<td style="text-align: center;">'.$Dth[$j][1].'</td>';
																	}
						$aut .= '						</tr>
															</tbody>															
														</table>';
						$aut .= '	</tbody>
										</table>';	
						
						/*	Display activation panel	*/
						$activation .= '<div class="panel panel-default">
															<div class="panel-heading">												 			
																<h4 class="panel-title">												 				
																	<a data-toggle="collapse" data-parent="#bccordion" href="#activation" title="Activation">Activation</a>
																</h4>
															</div>
															<div id="activation" class="panel-collapse collapse">
																<div class="panel-body">
																'.$aut.'
																</div>
															</div>
														</div>';

						/*	Display iteration table	*/
						$out .= '<table class="table table-striped">
											<tbody>';
						for($i=2; $i<=$epoch; $i++){
							$out .= '	<tr>
													<th colspan="'.$hidden.'">Epoch '.($i-1).'</th>
												</tr>
												<tr>
													<td>
														<table class="table table-bordered table-hover">
															<thead style="background: #F9F9F9;">
																<tr>																	
																	<th colspan="'.$hidden.'" style="text-align:center;">Actual Outputs</th>
																</tr>
																<tr>';
																	for($j=0; $j<$hidden; $j++){
																		$out .= '<th style="text-align:center;">z<sub>'.($j+1).'</sub></th>';
																	}
							$out .= '					</tr>
															</thead>
															<tbody>
																<tr>';
																	for($j=0; $j<$hidden; $j++){
																		$out .= '<td style="text-align:center;">'.$Z[$j][$i].'</td>';
																	}
							$out .= '					</tr>
															</tbody>															
														</table>
														<table class="table table-bordered table-hover">
															<thead style="background: #F9F9F9;">
																<tr>																	
																	<th colspan="'.$output.'" style="text-align:center;">Actual Outputs</th>
																</tr>
																<tr>';
																	for($j=0; $j<$output; $j++){
																		$out .= '<th style="text-align:center;">y<sub>'.($j+1).'</sub></th>';
																	}
							$out .= '					</tr>
															</thead>
															<tbody>
																<tr>';
																	for($j=0; $j<$output; $j++){
																		$out .= '<td style="text-align:center;">'.$Y[$j][$i].'</td>';
																	}
							$out .= '					</tr>
															</tbody>															
														</table>
														<table class="table table-bordered table-hover">
															<thead style="background: #F9F9F9;">
																<tr>																	
																	<th colspan="'.$output.'" style="text-align:center;">Error</th>
																	<th rowspan="2" style="text-align: center; vertical-align: middle;">Sum of Squared Error</th>
																</tr>
																<tr>';
																	for($j=0; $j<$output; $j++){
																		$out .= '<th style="text-align:center;">e<sub>'.($j+1).'</sub></th>';
																	}
							$out .= '					</tr>
															</thead>
															<tbody>
																<tr>';
																	for($j=0; $j<$output; $j++){
																		$out .= '<td style="text-align:center;">'.$error[$j][$i].'</td>';
																	}
							$out .= '						<td style="text-align: center;">'.(round($serror[$i],8)).'</td>
																</tr>
															</tbody>															
														</table>
														<table class="table table-bordered table-hover">
															<thead style="background: #F9F9F9;">
																<tr>																	
																	<th colspan="'.$output.'" style="text-align:center;">Error Gradient</th>
																</tr>
																<tr>';
																	for($j=0; $j<$output; $j++){
																		$out .= '<th style="text-align:center;">y<sub>'.($j+1).'</sub></th>';
																	}
							$out .= '					</tr>
															</thead>
															<tbody>
																<tr>';
																	for($j=0; $j<$output; $j++){
																		$out .= '<td style="text-align:center;">'.$errno[$j][$i].'</td>';
																	}
							$out .= '					</tr>
															</tbody>															
														</table>
														<table class="table table-bordered table-hover">
															<thead style="background: #F9F9F9;">
																<tr>																	
																	<th colspan="'.($output+1).'" style="text-align:center;">Weight Correction</th>
																<tr>
																	<th style="text-align: center; border-right: 2px solid #DDDDDD;">#</th>';
																	for($j=0; $j<$output; $j++){
																		$out .= '<th style="text-align: center;">y<sub>'.($j+1).'</sub></th>';
																	}												
							$out .= '					</tr>
															</thead>
															<tbody>';
																for($j=0; $j<$hidden; $j++){
																	$out .= '<tr>																														
																						<th style="background: #F9F9F9; text-align: center; border-right: 2px solid #DDDDDD;">z<sub>'.($j+1).'</sub></th>';
																	for($k=0; $k<$output; $k++){
																		$out .= '<td style="text-align: center;">'.$DW[$j][$k][$i].'</td>';
																	}
																	$out .= '</tr>';
																}							
							$out .= '					<tr>
																	<th style="background: #F9F9F9; text-align: center; border-right: 2px solid #DDDDDD;">θ</th>';
																	for($j=0; $j<$output; $j++){
																		$out .= '<td style="text-align: center;">'.$Dto[$j][$i].'</td>';
																	}
							$out .= '					</tr>
															</tbody>															
														</table>
														<table class="table table-bordered table-hover">
															<thead style="background: #F9F9F9;">
																<tr>																	
																	<th colspan="'.$hidden.'" style="text-align:center;">Error Gradient</th>
																</tr>
																<tr>';
																	for($j=0; $j<$hidden; $j++){
																		$out .= '<th style="text-align:center;">z<sub>'.($j+1).'</sub></th>';
																	}
							$out .= '					</tr>
															</thead>
															<tbody>
																<tr>';
																	for($j=0; $j<$hidden; $j++){
																		$out .= '<td style="text-align:center;">'.$errnh[$j][$i].'</td>';
																	}
							$out .= '					</tr>
															</tbody>															
														</table>
														<table class="table table-bordered table-hover">
															<thead style="background: #F9F9F9;">
																<tr>																	
																	<th colspan="'.($hidden+1).'" style="text-align:center;">Weight Correction</th>
																<tr>
																	<th style="text-align: center; border-right: 2px solid #DDDDDD;">#</th>';
																	for($j=0; $j<$hidden; $j++){
																		$out .= '<th style="text-align: center;">z<sub>'.($j+1).'</sub></th>';
																	}												
							$out .= '					</tr>
															</thead>
															<tbody>';
																for($j=0; $j<$input; $j++){
																	$out .= '<tr>																														
																						<th style="background: #F9F9F9; text-align: center; border-right: 2px solid #DDDDDD;">x<sub>'.($j+1).'</sub></th>';
																	for($k=0; $k<$hidden; $k++){
																		$out .= '<td style="text-align: center;">'.$DV[$j][$k][$i].'</td>';
																	}
																	$out .= '</tr>';
																}							
							$out .= '					<tr>
																	<th style="background: #F9F9F9; text-align: center; border-right: 2px solid #DDDDDD;">θ</th>';
																	for($j=0; $j<$hidden; $j++){
																		$out .= '<td style="text-align: center;">'.$Dth[$j][$i].'</td>';
																	}
							$out .= '					</tr>
															</tbody>															
														</table>';
							$out .= '		</td>
												</tr>';
						}
						$out .= '	</tbody>
										</table>';						

						/*	Display iteration panel	*/
						$iteration .= '<div class="panel panel-default">
															<div class="panel-heading">												 			
																<h4 class="panel-title">												 				
																	<a data-toggle="collapse" data-parent="#bccordion" href="#iteration" title="Iteration">Iteration</a>
																</h4>
															</div>
															<div id="iteration" class="panel-collapse collapse">
																<div class="panel-body">
																'.$out.'
																</div>
															</div>
														</div>';

						/*	Display Actual Outputs table	*/
						$aoutputs .= '<table class="table table-bordered table-hover">
													<thead style="background: #F9F9F9;">
														<tr>
															<th colspan="'.$output.'" style="text-align:center;">Actual Outputs</th>
														</tr>
														<tr>';
														for($i=0; $i<$output; $i++){
															$aoutputs .= '<th style="text-align:center;">y<sub>'.($i+1).'</sub></th>';
														}
						$aoutputs .= '	</tr>
													</thead>
													<tbody>
														<tr>';														
														for($i=0; $i<$output; $i++){
															$aoutputs .= '<td style="text-align: center;">'.$Y[$i][$epoch].'</td>';
														}
						$aoutputs .= '	</tr>
													</tbody>
												</table>';

						/*	Display Error table	*/
						$eoutputs .= '<table class="table table-bordered table-hover">
													<thead style="background: #F9F9F9;">
														<tr>
															<th colspan="'.$output.'" style="text-align:center;">Error</th>
															<th rowspan="2" style="text-align: center; vertical-align: middle;">Sum of Squared Error</th>
														</tr>
														<tr>';
														for($i=0; $i<$output; $i++){
															$eoutputs .= '<th style="text-align:center;">e<sub>'.($i+1).'</sub></th>';
														}
						$eoutputs .= '	</tr>
													</thead>
													<tbody>
														<tr>';
														for($i=0; $i<$output; $i++){															
															$eoutputs .= '<td style="text-align: center;">'.$error[$i][$epoch].'</td>';
														}
						$eoutputs .= '		<td style="text-align: center;">'.(round($serror[$epoch],8)).'</td>
														</tr>
													</tbody>
												</table>';

						/*	Display process panel	*/
						$process .= $init.
												$inputs.
												$doutputs.
												'<div class="panel-group" id="bccordion">
													'.$initialisation.'
													'.$activation.'
													'.$iteration.'
												</div>
												<br>'
												.$aoutputs
												.$eoutputs;
						
						/*	Display the Result of Disease	*/
						$content .= '<table class="table table-striped">
													<tbody>
														<tr>
															<th>Signs</th>
															<th>'.$disease[0]['name'].'</th>
															<th style="text-align: center;">(%)</th>
														</tr>
														<tr>
															<td>'.$asigns.'</td>																
															<td>'.$disease[0]['sign'].'</td>
															<td style="vertical-align: middle; text-align: center; font-weight: bold; font-size: 27px;">'.$disease[0]['percent'].'</td>
														</tr>
													</tbody>
												 </table>
												 <hr>
												 <div class="panel-group" id="accordion">
												 	<div class="panel panel-default">
												 		<div class="panel-heading">												 			
												 			<h4 class="panel-title">												 				
												 				<a data-toggle="collapse" data-parent="#accordion" href="#result" title="Other Result">Other Results</a>
												 			</h4>
												 		</div>
												 		<div id="result" class="panel-collapse collapse">
												 			<div class="panel-body">
												 				'.$result.'
												 			</div>
												 		</div>
												 	</div>
												 	<div class="panel panel-default">
												 		<div class="panel-heading">
												 			<h4 class="panel-title">
												 				<a data-toggle="collapse" data-parent="#accordion" href="#process" title="Process">Process</a>
												 			</h4>
												 		</div>
												 		<div id="process" class="panel-collapse collapse">
												 			<div class="panel-body">																
												 				'.$process.'												 				
												 				<i>Finished with '.($epoch+1).' epochs . . .</i>
												 			</div>
												 		</div>
												 	</div>
												 </div>';													 																							 						
					}
				}
			
				mysql_close();
				break;
			case 'disease' :
				mysql_open();

				/*	Get the data of Eye Diseases from database	*/
				$sql = "SELECT * FROM `Eye Disease`";
				$res = mysql_query($sql) or die(mysql_error());

				if(mysql_num_rows($res) > 0){
					/*	Show the results	*/
					$content .= '<table class="table table-striped">
												<tbody>
													<tr>
														<th>#</th>
														<th>Name</th>
														<th>Signs</th>
													</tr>';
				
					while($row = mysql_fetch_array($res)){
						$sql0 = "SELECT name FROM `Symptoms` WHERE id IN (".implode(', ', explode(' ', $row['signs'])).")";
						$res0 = mysql_query($sql0) or die(mysql_error());													
																 											
						$content .= '	<tr>
														<td>'.$row['id'].'</td>
														<td>'.$row['name'].'</td>
														<td>';																		
						if(mysql_num_rows($res0) > 0){
							$content .= '		<ul class="ul-table">';
							while($row0 = mysql_fetch_array($res0)){
								$content .= '		<li>'.$row0['name'].'</li>';
							}
							$content .= '		</ul>';
						}							
						$content .=		 '</td>
													</tr>';
					}
				
					$content .= '	</tbody>
											 </table>';										
				}

				mysql_close();
				break;
			default :
				mysql_open();

				/*	Get the data of Symptoms from database	*/
				$sql = "SELECT * FROM `Symptoms`";
				$res = mysql_query($sql) or die(mysql_error());

				if(mysql_num_rows($res) > 0){		
					/*	Show the results	*/
					$content .= '<form method="POST" action="?!=submit">
												<div class="table-responsive">
													<table class="table table-hover">
														<thead>
															<tr>
																<th style="text-align: center;">#</th>
																<th>Signs</th>
																<th style="text-align: center;"><input type="checkbox" disabled></th>
															</tr>
														</thead>
														<tbody>';

					while($row = mysql_fetch_array($res)){
						$content .= '			<tr>
																<td width="5%" align="center">'.$row['id'].'</td>
																<td>'.$row['name'].'</td>
																<td width="5%" align="center"><input type="checkbox" name="symptoms[]" value="'.$row['id'].'"></td>
															</tr>';
					}
				
					$content .= '			</tbody>
													</table>
												</div>	
												<div align="right">
													<input class="btn btn-default" type="submit" name="submit" value="submit">										
												</div>
											</form>';
				}
			
				mysql_close();
		}
		echo $content;	
	}
?>

<!doctype html>
<html>
<head>
	<meta charset="UTF-8">	
	<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>			
	<link rel="stylesheet" href="bootstrap.min.css" type="text/css">
	<link rel="stylesheet" href="style.css" type="text/css">	
	<title>Artificial Neural Network on Disease</title>
</head>

<body>
	<div class="container">
		<div class="header"><?php head(); ?></div>
		<div class="navigation"><?php navigation(); ?></div>
		<div class="content"><?php main(); ?></div>
		<div class="footer"><?php footer(); ?></div>
	</div>	
	<!-- jQuery -->
	<script src="jquery.js" type="text/javascript"></script>
  <!-- Bootstrap -->
  <script src="bootstrap.min.js" type="text/javascript"></script>	
</body>
</html>
