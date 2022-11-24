<div class="row consignment">
    <div class="col-12 text-center">
        <span class="form-title"> @lang('station.votes_desc') </span>
        <span class="close-page">
            <button type="button" class="btn btn-circle close" data-dismiss="modal" aria-label="Close">
                <span class="icon-delete">X</span>
            </button>
        </span>
    </div>
    <div class="col-12"><hr class="clearfix"></div>
    <div class="col-12">
        <div class="radio-group row px-3" >
            @if(isset($company) && count($company) > 0)
                @foreach($company as $key => $siraValue)
                    @if(count($siraValue) > 0 )
                        @foreach($siraValue as $value)
                        <div class="col-sm-6">
                            <div 
                                class="card py-0 radio one text-center"  
                                id="{{ $value['id'] }}"
                                style="width:100%; margin-bottom:10px;"
                            >
                                <img 
                                    class="card-img-top" 
                                    src="/upload/images/consignees/{{ $value['logo'] }}" 
                                    alt="{{ $value['name'] }}"
                                    style="width:auto!important; height:60px;"
                                >
                                <div class="card-body">
                                    <span class="card-title">{{ $value['name'] }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                @endforeach
            @endif
            @if(isset($company) && count($company) > 0)
            <div class="col-sm-6">
            @else
            <div class="col-sm-12">
            @endif
                <div 
                    class="card py-0 radio one text-center"  
                    id="other"
                    style="width:100%; margin-bottom:10px;"
                >
                    <img 
                        class="card-img-top" 
                        src="/upload/images/consignees/plus.png" 
                        alt="@lang('station.diger')"
                        style="width:auto!important; height:60px;"
                    >
                    <div class="card-body">
                        <span class="card-title">@lang('station.diger')</span>
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="row ">
            <div class="col">
                <p class="text-muted">Tek bir seçenek seçin</p>
            </div>
        </div> --}}
        <div class="col-12"><hr class="clearfix"></div>
        {{-- <div class="row">
            <div class="col-12 text-center">
                <button type="button" id="consignmentNextBtn" class="btn btn-primary" style="display:none;">
                    Devam Et
                    <span class="ml-2">
                        <i class="fa fa-angle-right" aria-hidden="true"></i>
                    </span>
                </button>
            </div>
        </div> --}}
    </div>
</div>

<link rel="stylesheet" type="text/css" href="/station/css/votes.css">
<script>

    $(document).ready(function () {
        // radio count elemnts
        lengs = $(".radio").length;
        
        $('.radio-group .radio').click(function () {
            $('.selected .fa').removeClass('fa-check');
            $('.radio').removeClass('selected');
            $(this).addClass('selected');
            $('.one').css( "pointer-events", "none" );
            id = $(this).attr('id');
            localStorage.setItem('etiket', id);
            
            axios({

                url : "{{ route('station.viewSor') }}",
                method : 'post',
                data : {
                    consingneeId : id,
                }
                
            }).then(function (response) {

                if (response.data.other == true){
                    localStorage.setItem('other', response.data.other);
                }

                getPage(response.data.view);

            }).catch(function (error) {

                console.error(error.response.data);

            });
        });

    });
    
    
    $(".close-page").on('click', function (){
        window.localStorage.removeItem('etiket');
    });

</script>
