<template>

<div>
    

    <transition
    name="custom-classes-transition"
    enter-active-class="animated slideInDown"
    leave-active-class="animated bounceOutRight"
  >


        <div class="kt-portlet kt-portlet--mobile" >
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon">
                        <i class="kt-font-brand flaticon2-line-chart"></i>
                    </span>
                    <h3 class="kt-portlet__head-title">
                        Sayım Listesi
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">
                            <div class="dropdown dropdown-inline">
                                <button type="button" class="btn btn-default btn-icon-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="la la-download"></i> Çıkart
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <ul class="kt-nav">
                                        <li class="kt-nav__section kt-nav__section--first">
                                            <span class="kt-nav__section-text">Seçiniz</span>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="#" class="kt-nav__link">
                                                <i class="kt-nav__link-icon la la-print"></i>
                                                <span class="kt-nav__link-text">Print</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="#" class="kt-nav__link">
                                                <i class="kt-nav__link-icon la la-file-excel-o"></i>
                                                <span class="kt-nav__link-text">Excel</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="#" class="kt-nav__link">
                                                <i class="kt-nav__link-icon la la-file-text-o"></i>
                                                <span class="kt-nav__link-text">CSV</span>
                                            </a>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a href="#" class="kt-nav__link">
                                                <i class="kt-nav__link-icon la la-file-pdf-o"></i>
                                                <span class="kt-nav__link-text">PDF</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                          
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <!--begin: Datatable -->
                <table class="table table-striped- table-bordered table-hover table-checkable slideInDown " id="deviceList">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th width="150">EPC</th>
                       
                    </tr>
                    </thead>
                    <tbody v-for="data in value">
                    
                        <tr>
                            <td align="">
                               {{ data.id }}
                            </td>
                            <td>{{ data.epc }}</td>
                            
                            
                        </tr>
                   
                    </tbody>
                </table>
                <!--end: Datatable -->
            </div>
        </div>
</transition>
 

</div>
    


</template>

<script>

    export default{
        
         
                        data: function(){
                            return{
                                table: false,
                                errored : false,
                                value : [],
                                show: false,
                                test: 0,
                                timer: null,
                                disabled: false,
                               
                                
                                }
                            },
                            
                            computed: {
                                start: function(event){

                                        this.table = true;
                                        this.disabled = true;
                                        this.timer = setInterval(this.getData, 1000);
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