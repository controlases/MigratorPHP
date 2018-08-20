<?php require_once("libs/config.php");?>
<!DOCTYPE html>
<html>
<head>
	<title>Migrate to a to</title>
	<link rel="stylesheet" type="text/css" href="http://siscontable.com/css/app.css">
	<link rel="stylesheet" type="text/css" href="assets/style.css">
	<meta name="csrf-token" content="xxx">
</head>
<body>


<div class="container" id="app">

	<div id="loader" v-if="loading">
		<div class="spinner"></div>
	</div>

	<div class="valign all-height">
		<div class="all-width">
			
			<div class="row">
				<div class="col-md-4">
					<div class="card">
						<div class="card-header">
							Migrate projects
						</div>
						<div class="card-body">
							if your project has plain html or flat php, so you can run this script to convert it into a laravel project.
						</div>
					</div>
				</div>
				<div class="col-md-8">
					
						
					<div class="card">
						<div class="card-header">
							Migrate projects
						</div>
						<div class="card-body">
							<div v-if="result == null">
								<div >
									<template v-if="step == 1">
										
										<div class="input-row">
											<input type="text" placeholder="#wrapper" v-model="data.el">
											<label>Css Selector of Content (Apply for all views)</label>
										</div>

										<div>
											Unique CSS Selector.
										</div>
									</template>

									<template v-if="step == 2">
										<div class="input-row">
											<input type="text" placeholder="project" v-model="data.name" v-on:change="bufferName">
											<label>Project Name</label>
											<div>
												
											</div>
										</div>
										
									</template>

									<template v-if="step == 3">
										<div class="input-row">
											<input type="text" placeholder="/path/to" v-model="data.folder">
											<label>Folder</label>
										</div>

										<div>Project directory</div>
										<button type="submit" class="btn btn-primary"  v-on:click="submit">Save</button>
									</template>



								</div>

								<button type="button" class="btn btn-primary" v-if="step < 3" v-on:click="next">Next</button>

							</div>


							<div v-if="result">

								<ul class="list-group list-group-flush">
									<li class="list-group-item">
										<div class="row">
											<div class="col-md-8">Processed</div>
											<div class="col-md-4">{{result.proccesed}}</div>

											<div class="col-md-8">Status</div>
											<div class="col-md-4">{{result.status}}</div>

											<div class="col-md-8">Output Path</div>
											<div class="col-md-4">{{result.output_directory}}</div>
										</div>
									</li>
								</ul>
							
								<p>Sql: </p>
								<div class="cd" v-html="result.sql">
								</div>

								<h2>Route Service Provider: </h2>
								<div class="cd">
									{{result.routeservice}}
								</div>

								<h2>Route Service Boot: </h2>
								<div class="cd">
									{{result.routeboot}}
								</div>

								
								<a href="#!" class="btn btn-primary" v-on:click="restart">Start again</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	</div>
<script src="https://cdn.jsdelivr.net/npm/vue@2.5.17/dist/vue.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.18.0/axios.js" ></script>

<script type="text/javascript">
	
	var app2 = new Vue({
		el:"#app",
		data:function(){
			return {
				step:1,
				data:{},
				result:null,
				loading:false
			};
		},
		created: function(){
			this.fillData();
		},
		methods:{
			fillData(){
				this.data = {el:".section2 .container", folder:"<?="/home/jochoar2/public_html/".PROJECT_NAME;?>", name:""};
			},
			next:function(event){
				this.step++;
			},
			submit(){
				this.loading = true;
				axios.get(`process.php?name=${this.data.name}&folder=${this.data.folder}&el=${this.data.el}`).then(data => {
					this.result = data.data;
					this.loading = false;
				}).catch(error => {

				});
			},
			restart(){
				this.result = null;
				this.step = 1;
				this.fillData();
			},
			bufferName(event){
				this.data.folder += event.target.value;
			}
		}
	});
</script>
</body>
</html>