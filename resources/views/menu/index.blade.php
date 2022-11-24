@extends('layouts.main')

@section('content')

<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

    <!-- begin:: Subheader -->
    <div class="kt-subheader kt-grid__item" id="kt_subheader">
        <div class="kt-container  kt-container--fluid ">
            <div class="kt-subheader__main">
                <h3 class="kt-subheader__title">
                    @lang('portal.menus')
                </h3>
                <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                <div class="kt-subheader__group" id="kt_subheader_search">
                    <span class="kt-subheader__desc" id="kt_subheader_total">@lang('portal.menu_list')</span>
                </div>
            </div>
            <div class="kt-subheader__toolbar"></div>
        </div>
    </div>

    <!-- end:: Subheader -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="row">
            <div class="col-lg-7">
                <div class="kt-portlet kt-portlet--mobile">
                    <div class="kt-portlet__head kt-portlet__head--lg">
                        <div class="kt-portlet__head-label">
                            <span class="kt-portlet__head-icon">
                                <i class="kt-font-brand flaticon-map"></i>
                            </span>
                            <h3 class="kt-portlet__head-title">
                                @lang('portal.menu_list')
                            </h3>
                        </div>
                    </div>
                    <div class="kt-portlet__body">

                        <div class="dd" id="nestable">
                            <ol class="dd-list">
                                @foreach($menus as $key => $value)
                                @if($value->id>0)
                                    <li class="dd-item dd3-item" data-id="{{ $value->id }}">
                                        <div class="dd-handle dd3-handle"></div>
                                        <div class="dd3-content">
                                            <i class="{{ $value->icon }}"></i> {{ $value->title }} - {{ $value->title_en }}
                                            <span class="float-right">
                                            <a href="{{ route('menu.edit', $value->id) }}" title="@lang('portal.edit')"><i class="fa fa-edit"></i></a> &nbsp;
                                            <a href="{{ route('menu.destroy', $value->id) }}" title="@lang('portal.delete')" data-method="delete" data-token="{{csrf_token()}}" data-confirm="@lang('portal.delete_text')"><i class="fa fa-trash"></i></a>
                                        </span>
                                        </div>
                                        @if(isset($value->children) && count($value->children) > 0)
                                            <ol class="dd-list">
                                                @foreach($value->children as $key2 => $value2)
                                                    <li class="dd-item dd3-item" data-id="{{ $value2->id }}">
                                                        <div class="dd-handle dd3-handle"></div>
                                                        <div class="dd3-content">
                                                            <i class="{{ $value2->icon }}"></i> {{ $value2->title }} - {{ $value2->title_en }}
                                                            <span class="float-right">
                                                            <a href="{{ route('menu.edit', $value2->id) }}" title="@lang('portal.edit')"><i class="fa fa-edit"></i></a> &nbsp;
                                                            <a href="{{ route('menu.destroy', $value2->id) }}" title="@lang('portal.delete')" data-method="delete" data-token="{{csrf_token()}}" data-confirm="@lang('portal.delete_text')"><i class="fa fa-trash"></i></a>
                                                        </span>
                                                        </div>
                                                        @if(isset($value2->children) && count($value2->children) > 0)
                                                            <ol class="dd-list">
                                                                @foreach($value2->children as $key3 => $value3)
                                                                    <li class="dd-item dd3-item" data-id="{{ $value3->id }}">
                                                                        <div class="dd-handle dd3-handle"></div>
                                                                        <div class="dd3-content">
                                                                            <i class="{{ $value3->icon }}"></i> {{ $value3->title }} - {{ $value3->title_en }}
                                                                            <span class="float-right">
                                                                            <a href="{{ route('menu.edit', $value3->id) }}" title="@lang('portal.edit')"><i class="fa fa-edit"></i></a> &nbsp;
                                                                            <a href="{{ route('menu.destroy', $value3->id) }}" title="@lang('portal.delete')" data-method="delete" data-token="{{csrf_token()}}" data-confirm="@lang('portal.delete_text')"><i class="fa fa-trash"></i></a>
                                                                        </span>
                                                                        </div>
                                                                    </li>
                                                                @endforeach
                                                            </ol>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ol>
                                        @endif
                                    </li>
                                @endif
                            @endforeach
                        </ol>
                     </div>



                        <input type="hidden" id="nestable-output">
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="kt-portlet kt-portlet--mobile">
                    <div class="kt-portlet__head kt-portlet__head--lg">
                        <div class="kt-portlet__head-label">
                            <span class="kt-portlet__head-icon">
                                <i class="kt-font-brand flaticon-map"></i>
                            </span>
                            <h3 class="kt-portlet__head-title">
                                @lang('portal.add_new_menu')
                            </h3>
                        </div>
                    </div>
                    <form class="kt-form" id="menu_form" action="{{ route('menu.store') }}" method="post">
                        @csrf
                        <div class="kt-portlet__body">
                            <div class="form-group row">
                                <label class="col-3 col-form-label">@lang('portal.top_menu')</label>
                                <div class="col-9">
                                    <select name="parent_id" id="parent_id" class="form-control select @error('parent_id') is-invalid @enderror">
                                        <option value="0">@lang('portal.main_menu')</option>
                                        @foreach($menus as $key => $value)
                                            <option value="{{ $value->id }}"  {{ old('parent_id') == $value->id ? 'selected' : '' }}> {{ $value->title }} - {{ $value->title_en }} </option>
                                            @if(isset($value->children) && count($value->children) > 0)
                                                @foreach($value->children as $key2 => $value2)
                                                    <option value="{{ $value2->id }}"  {{ old('parent_id') == $value2->id ? 'selected' : '' }}> {{ $value2->title }} - {{ $value2->title_en }} </option>
                                                    @if(isset($value2->children) && count($value2->children) > 0)
                                                        @foreach($value2->children as $key3 => $value3)
                                                            <option value="{{ $value3->id }}"  {{ old('parent_id') == $value3->id ? 'selected' : '' }}> {{ $value3->title }} - {{ $value3->title_en }} </option>
                                                        @endforeach
                                                    @endif
                                                @endforeach
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('parent_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-3 col-form-label">@lang('portal.icon')</label>
                                <div class="col-9">
                                    <select name="icon" id="icon" data-placeholder="Seçiniz..." class="select-icons form-control @error('icon') is-invalid @enderror">
                                        @foreach(Config::get('settings.fa_icon') as $key => $value)
                                            <option value="{{ $key }}" data-icon="{{ $key }}"  {{ old('icon') == $key ? 'selected' : '' }}>{{ $value }} </option>
                                        @endforeach
                                    </select>
                                    @error('icon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-3 col-form-label">@lang('portal.menu_name')</label>
                                <div class="col-9">
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}">
                                    @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-3 col-form-label">@lang('portal.menu_name') En</label>
                                <div class="col-9">
                                    <input type="text" class="form-control @error('title_en') is-invalid @enderror" name="title_en" value="{{ old('title_en') }}">
                                    @error('title_en')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-3 col-form-label">Link</label>
                                <div class="col-9">
                                    <input type="text" class="form-control @error('link') is-invalid @enderror" name="link" value="{{ old('link') }}">
                                    @error('link')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-3 col-form-label">@lang('portal.role')</label>
                                <div class="col-9">
                                    <select name="roles[]" id="roles" multiple="multiple" class="form-control select @error('roles') is-invalid @enderror">
                                        @foreach($roles as $val)
                                            <option value="{{ $val->id }}">{{ $val->title }} </option>
                                        @endforeach
                                    </select>
                                    @error('roles')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="kt-portlet__foot">
                            <div class="kt-form__actions">
                                <button type="submit" class="btn btn-primary">@lang('portal.save')</button>
                                <button type="reset" class="btn btn-secondary">@lang('portal.clean')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('css')

    <link href="/assets/css/nestable.css" rel="stylesheet" type="text/css">

@endsection

@section('js')
    <script src="/assets/js/jquery.nestable.js"></script>
    <script>
        $(function() {

            function iconFormat(icon) {
                var originalOption = icon.element;
                if (!icon.id) { return icon.text; }
                var $icon = "<i class='" + $(icon.element).data('icon') + "'></i> " + icon.text;

                return $icon;
            }

            $(".select-icons").select2({
                templateResult: iconFormat,
                templateSelection: iconFormat,
                escapeMarkup: function(m) { return m; }
            });

            $('.select').select2({
                minimumResultsForSearch: Infinity
            });

            var updateOutput = function(e)
            {
                var list   = e.length ? e : $(e.target),
                    output = list.data('output');
                if (window.JSON) {
                    output.val(window.JSON.stringify(list.nestable('serialize')));//, null, 2));
                } else {
                    output.val('JSON browser support required for this demo.');
                }
            };

            // activate Nestable for list 1
            $('#nestable').nestable().on('change', updateOutput);

            // output initial serialised data
            updateOutput($('#nestable').data('output', $('#nestable-output')));

            $('.dd').on('change', function(e) {
                e.preventDefault();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                var dataString = {
                    data : $("#nestable-output").val()
                };
                $.ajax({
                    type: "POST",
                    url: "{{ route('menu.sort') }}",
                    data: dataString,
                    cache : false,
                    beforeSend: function () {},
                    success: function(data){},
                    error: function(xhr, status, error) {
                        console.log(status);
                    },
                });
            });

        });
    </script>
@endsection
