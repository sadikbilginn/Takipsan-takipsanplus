@extends('layouts.main')

@section('content')
    <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
        <!-- begin:: Subheader -->
        <div class="kt-subheader kt-grid__item" id="kt_subheader">
            <div class="kt-container  kt-container--fluid ">
                <div class="kt-subheader__main">
                    <h3 class="kt-subheader__title">
                        @lang('portal.consignments')
                    </h3>
                    <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                    <div class="kt-subheader__group" id="kt_subheader_search">
                        <span class="kt-subheader__desc" id="kt_subheader_total">@lang('portal.new_consignment')</span>
                    </div>
                </div>
                <div class="kt-subheader__toolbar"></div>
            </div>
        </div>
        <!-- end:: Subheader -->
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="row">
                <div class="col-lg-12">
                    <!--begin::Portlet-->
                    <div 
                        class="kt-portlet kt-portlet--last kt-portlet--head-lg kt-portlet--responsive-mobile" 
                        id="kt_page_portlet"
                    >
                        <div class="kt-portlet__head kt-portlet__head--lg">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    @lang('portal.new_consignment') <small> @lang('portal.form_text')</small>
                                </h3>
                            </div>
                            <div class="kt-portlet__head-toolbar">
                                <a href="javascript:history.go(-1);" class="btn btn-clean kt-margin-r-10">
                                    <i class="la la-arrow-left"></i>
                                    <span class="kt-hidden-mobile">@lang('portal.back')</span>
                                </a>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-brand" id="consignmentSubmitBtn">
                                        <i class="la la-check"></i>
                                        <span class="kt-hidden-mobile">@lang('portal.save')</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="kt-portlet__body">
                            <div class="row">
                                <div class="col-xl-2"></div>
                                <div class="col-xl-8">
                                    <div class="kt-section kt-section--first">
                                        <div class="kt-section__body">
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label">@lang('portal.consignee_name')</label>
                                                <div class="col-9">
                                                    <select 
                                                        name="vote_id" 
                                                        id="vote_id" 
                                                        class="form-control select @error('vote_id') is-invalid @enderror"
                                                    >
                                                        <option value="" selected>@lang('portal.choose')</option>
                                                        @foreach($company as $key => $value)
                                                        <option value="{{ $value->id }}"> {{$value->name}} </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="vote_form"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Portlet-->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    
    <script>

        $(document).ready(function () {

            $('#vote_id').on('change', function () {
                
                var id = $(this).val();

                if(id==''){
                    $('.vote_form').html('');
                }else{

                    axios({
                        
                        url : "{{ route('consignment.viewSor') }}",
                        method : 'post',
                        data : {
                            consingneeId : id,
                        }

                    }).then(function (response) {
                        
                        axios({
                            url : "{{ route('consignment.store') }}",
                            method : 'post',
                            data : {
                                process : response.data.view,
                                param : 1,
                                consingneeId : id
                            }

                        }).then(function (response) {
                            
                            $('.vote_form').html(response.data.html);

                        }).catch(function (error) {
                            console.log(error);
                        });

                        // if (response.data.other == true){
                        //     localStorage.setItem('other', response.data.other);
                        // }
                        // getPage(response.data.view);

                    }).catch(function (error) {
                        console.error(error.response);
                    });
                }

            });

        });
        
        $(function() {
            $('.select').select2();
        });
    </script>

@endsection