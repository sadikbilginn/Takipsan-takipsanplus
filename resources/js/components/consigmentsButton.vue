
	<template>
		
           <div class="kt-input-icon kt-input-icon--right kt-subheader__search">
                <button href="javascript:;" class="btn btn-success btn-md" v-if="!disabled" :disabled=disabled v-on:click="startProcess">Sevkiyatı Başlat <i style="margin-left: 6px" class="fa fa-play"></i></button>

                <button href="javascript:;" class="btn btn-danger btn-md" v-if="disabled" v-on:click="stopProcess">Sevkiyatı Durdur <i style="margin-left: 6px" class="fa fa-stop"></i></button>
               
            </div>


            </template>

                <script>
                
                	export default {
                
                		data: function(){
                			return{
                			    table: false,
				                errored : false,
				                show: false,
				                test: 0,
				                timer: null,
				                disabled: false,
                                value : [],
				                
                               

                                }
                			},
                            methods: {
                                startProcess: function(event){
                                    this.$root.$emit('start');
                                        this.table = true;
                                        this.disabled = true;
                                        this.timer = setInterval(this.getData, 3000);
                                        document.getElementById('sevkiyatTable').style.display = "block";
                                        
                                },
                                stopProcess: function(event){
                                  clearInterval(this.timer);
                                  this.disabled = false;
                                  //this.timer = undefined;
                                },
                                 getData: function(limit){
                                    var a = this.timer++;
                                   axios
                            .get('http://localhost/test/'+a).then(response => {this.value = response.data;}).catch(error => {console.log(error);this.errored = true})
                      .finally(() => this.time = false)

                            }

                        },

                            }
                		
                	
                	

                

                
                </script>

