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
	function bsort(&$a){
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
								$symptoms[] = array('name' => $row['name'],
																		'sign' => $sign, 
																		'percent' => percent($_POST['symptoms'], explode(' ', $row['signs']))
																	 );
							}							
						}

						/*	Sort the array of symptoms to Descending	*/
						bsort($symptoms);												
						
						/*	Show the Other Result of Diseases	*/
						$result .= '<table class="table table-striped">
												 	<tbody>';
						for($i=1; $i<count($symptoms); $i++){
							$result .= '	<tr>
															<th>Signs</th>
															<th>'.$symptoms[$i]['name'].'</th>
															<th style="text-align: right; padding-right: 12px;">(%)</th>
														</tr>
														<tr>
															<td>'.$asigns.'</td>																
															<td>'.$symptoms[$i]['sign'].'</td>
															<td style="vertical-align: middle; text-align: center; font-weight: bold; font-size: 27px;">'.$symptoms[$i]['percent'].'</td>
														</tr>';
						}
						$result .= '	</tbody>
												</table>';
						
						/*
							Jumlah neuron pada input layer = count($_POST['symptoms'])
							Jumlah neuron pada hidden layer = 4
							Jumlah neuron pada output layer = 1
							Learning rate = 0.1
							Target Error = 0.001
							Bobot Awal input[i] ke hidden[h] (v) : rand(-0.9, 0.9)
							Bobot Awal bias[b] ke hidden[h] (bh) : rand(-0.9, 0.9)
							Bobot Awal hidden[h] ke output[o] (w) : rand(-0.9, 0.9)
							Bobot Awal bias ke output (b2) : rand(-0.9, 0.9)
							
						*/
						
						$sql1 = "SELECT * FROM `Symptoms`";
						$res1 = mysql_query($sql1) or die(mysql_error());

						$input = mysql_num_rows($res1);
						$hidden = 9;
						$output = 8;
						$lrate = 0.1;
						$etarget = 0.001;
						$mepoch = 1000;
						
						$process .= '<table class="table table-bordered">
													<thead style="background: #F9F9F9;">
														<tr>
															<th colspan="3" style="text-align: center;">Sum of Neuron</th>
															<th rowspan="2" style="text-align: center; vertical-align: middle;">Learning Rate</th>
															<th rowspan="2" style="text-align: center; vertical-align: middle;">Error Target</th>
															<th rowspan="2" style="text-align: center; vertical-align: middle;">Max Epoch</th>
														</tr>
														<tr>
															<th style="text-align: center;">Input Layer</th>
															<th style="text-align: center;">Hidden Layer</th>
															<th style="text-align: center;">Output Layer</th>															
														</tr>
													</thead>
													<tbody style="text-align: center;">
														<tr>
															<td>'.$input.'</td>
															<td>'.$hidden.'</td>
															<td>'.$output.'</td>
															<td>'.$lrate.'</td>
															<td>'.$etarget.'</td>
															<td>'.$mepoch.'</td>
														</tr>
													</tbody>
												 </table>';

						/*	Random initial weight input to hidden	*/
						for($i=0; $i<$input; $i++){
							for($j=0; $j<$hidden; $j++){
								/*	Random between -0.9 and 0.9	*/
								$V[$i][$j] = round(rand()/getrandmax()*1.8-0.9, 4);
							}
						}

						/*	Random initial weight bias to hidden	*/						
						for($i=0; $i<$hidden; $i++){
							/*	Random between -0.9 and 0.9	*/
							$bh[$i] = round(rand()/getrandmax()*1.8-0.9, 4);
						}

						/*	Random initial weight hidden to output	*/
						for($i=0; $i<$hidden; $i++){
							for($j=0; $j<$output; $j++){
								/*	Random between -0.9 and 0.9	*/
								$W[$i][$j] = round(rand()/getrandmax()*1.8-0.9, 4);
							}
						}

						/*	Random initial weight bias to output	*/						
						for($i=0; $i<$output; $i++){
							/*	Random between -0.9 and 0.9	*/
							$bo[$i] = round(rand()/getrandmax()*1.8-0.9, 4);
						}

						$initial .= '<ul class="ul-table">
													<li>Initial weight input layer <i>x</i> to hidden layer <i>h</i></li>
														<p>
															<table class="table table-bordered table-hover">
																<thead>
																	<tr>
																		<th style="text-align: center; border-right: 2px solid #DDDDDD;">#</th>';
																		for($i=0; $i<$hidden; $i++){
																			$initial .= '	<th style="text-align: center;">h<sub>'.($i+1).'</sub></th>';
																		}
										$initial .= '	</tr>
																</thead>
																<tbody>';
																	for($i=0; $i<$input; $i++){
																		$initial .= '<tr>																														
																										<th style="text-align: center; border-right: 2px solid #DDDDDD;">x<sub>'.($i+1).'</sub></th>';
																		for($j=0; $j<$hidden; $j++){
																			$initial .= '	<td style="text-align: center;">'.$V[$i][$j].'</td>';
																		}
																		$initial .= '</tr>';
																	}
										$initial .= '</tbody>
															</table>
														</p>
													<li>Initial weight bias layer <i>b</i> to hidden layer <i>h</i></li>
														<p>
															<table class="table table-bordered table-hover">
																<thead>
																	<tr>
																		<th style="text-align: center; border-right: 2px solid #DDDDDD;">#</th>';
																		for($i=0; $i<$hidden; $i++){
																			$initial .= '	<th style="text-align: center;">h<sub>'.($i+1).'</sub></th>';
																		}
										$initial .= '	</tr>
																</thead>
																<tbody>
																	<tr>
																		<th style="text-align: center; border-right: 2px solid #DDDDDD;">b</th>';
																	for($i=0; $i<$hidden; $i++){
																		$initial .= '<td style="text-align: center;">'.$bh[$i].'</td>';
																	}
										$initial .= '	</tr>
																</tbody>
															</table>
														</p>
													<li>Initial weight hidden layer <i>h</i> to output layer <i>y</i></li>
														<p>
															<table class="table table-bordered table-hover">
																<thead>
																	<tr>
																		<th style="text-align: center; border-right: 2px solid #DDDDDD;">#</th>';
																		for($i=0; $i<$output; $i++){
																			$initial .= '	<th style="text-align: center;">y<sub>'.($i+1).'</sub></th>';
																		}
										$initial .= '	</tr>
																</thead>
																<tbody>';
																	for($i=0; $i<$hidden; $i++){
																		$initial .= '<tr>																														
																										<th style="text-align: center; border-right: 2px solid #DDDDDD;">h<sub>'.($i+1).'</sub></th>';
																		for($j=0; $j<$output; $j++){
																			$initial .= '	<td style="text-align: center;">'.$W[$i][$j].'</td>';
																		}
																		$initial .= '</tr>';
																	}
										$initial .= '</tbody>
															</table>
														</p>
													<li>Initial weight bias layer <i>b</i> to output layer <i>y</i></li>
														<p>
															<table class="table table-bordered table-hover">
																<thead>
																	<tr>
																		<th style="text-align: center; border-right: 2px solid #DDDDDD;">#</th>';
																		for($i=0; $i<$output; $i++){
																			$initial .= '	<th style="text-align: center;">y<sub>'.($i+1).'</sub></th>';
																		}
										$initial .= '	</tr>
																</thead>
																<tbody>
																	<tr>
																		<th style="text-align: center; border-right: 2px solid #DDDDDD;">b</th>';
																	for($i=0; $i<$output; $i++){
																		$initial .= '<td style="text-align: center;">'.$bh[$i].'</td>';
																	}
										$initial .= '	</tr>
																</tbody>
															</table>
														</p>
												</ul>';

						$process .= '<div class="panel-group" id="bccordion">
												 	<div class="panel panel-default">
												 		<div class="panel-heading">												 			
												 			<h4 class="panel-title">												 				
												 				<a data-toggle="collapse" data-parent="#bccordion" href="#initial" title="Click to Show">Initial</a>
												 			</h4>
												 		</div>
												 		<div id="initial" class="panel-collapse collapse">
												 			<div class="panel-body">
												 				'.$initial.'
												 			</div>
												 		</div>
												 	</div>												 	
												 </div>';
						
						/*	Show the Result of Disease	*/
						$content .= '<table class="table table-striped">
													<tbody>
														<tr>
															<th>Signs</th>
															<th>'.$symptoms[0]['name'].'</th>
															<th style="text-align: center;">(%)</th>
														</tr>
														<tr>
															<td>'.$asigns.'</td>																
															<td>'.$symptoms[0]['sign'].'</td>
															<td style="vertical-align: middle; text-align: center; font-weight: bold; font-size: 27px;">'.$symptoms[0]['percent'].'</td>
														</tr>
													</tbody>
												 </table>
												 <hr>
												 <div class="panel-group" id="accordion">
												 	<div class="panel panel-default">
												 		<div class="panel-heading">												 			
												 			<h4 class="panel-title">												 				
												 				<a data-toggle="collapse" data-parent="#accordion" href="#result" title="Click to Show">Other Results</a>
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
												 				<a data-toggle="collapse" data-parent="#accordion" href="#process" title="Click to Show">Process</a>
												 			</h4>
												 		</div>
												 		<div id="process" class="panel-collapse collapse">
												 			<div class="panel-body">
												 				'.$process.'
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
