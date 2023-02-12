@extends('layouts.app')

@section('content')
<div class="container">
{{--    <div class="row">--}}
{{--        <div class="col-md-12 col-lg-12">--}}
{{--            <div class="card">--}}
{{--                <div class="card-header">{{ __('Dashboard') }}</div>--}}
{{--                <div class="card-body">--}}
{{--                    @if (session('success'))--}}
{{--                        <div class="alert alert-success" role="alert">--}}
{{--                            {{ session('success') . ' '. session('file') .' với số dòng là: '. number_format(session('count')) }}--}}
{{--                        </div>--}}
{{--                    @endif--}}
{{--                    @if (session('error'))--}}
{{--                        <div class="alert alert-danger" role="alert">--}}
{{--                            {{ session('error') }}--}}
{{--                        </div>--}}
{{--                    @endif--}}
{{--                    <p>Số dòng còn lại: {{ $textCount }}</p>--}}
{{--                    <p>Xin hãy tải lên file <strong style="color: red">txt </strong></p>--}}
{{--                    form upload file accpect excel, csv, doc, txt, --}}
{{--                    <form class="d-flex " action="{{route('upload')}}" method="post" enctype="multipart/form-data" id="form-upload">--}}
{{--                        @csrf--}}
{{--                        <input type="file" class="form-control w-auto" name="file" id="file" accept=".txt">--}}
{{--                        <button type="submit" id="btn-submit" class="btn btn-success mx-1">Tải lên</button>--}}
{{--                    </form>--}}
{{--                    <p class="mb-0 mt-2">--}}
{{--                        <b id="info" ></b>--}}
{{--                    </p>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
    <div id="app"></div>
</div>
@endsection
@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.3.js" integrity="sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM=" crossorigin="anonymous"></script>
    <script>
        {{--$(document).ready(function () {--}}
        {{--    $('#file').change(function () {--}}
        {{--        const file = this.files[0];--}}
        {{--        const fileType = file.type;--}}
        {{--        const match = ['text/plain'];--}}
        {{--        if (!((fileType === match[0]))) {--}}
        {{--            alert('Chỉ được upload file txt');--}}
        {{--            $('#file').val('');--}}
        {{--            return false;--}}
        {{--        } else {--}}
        {{--            let formData = new FormData($('#form-upload')[0]);--}}
        {{--            $.ajax({--}}
        {{--                url: '{{ route('count-row') }}',--}}
        {{--                type: 'POST',--}}
        {{--                data: formData,--}}
        {{--                contentType: false,--}}
        {{--                processData: false,--}}
        {{--                dataType: 'json',--}}
        {{--                success: function (response) {--}}
        {{--                    $('#btn-submit').attr('disabled', false);--}}
        {{--                    $('#btn-submit').text('Tải lên');--}}
        {{--                    $('#info').html('Số dòng trong file là: ' + new Intl.NumberFormat().format(response.count)).css('color', '#198754');--}}
        {{--                },--}}
        {{--                beforeSend: function () {--}}
        {{--                    $('#btn-submit').attr('disabled', true);--}}
        {{--                    $('#btn-submit').text('Đang xử lý...');--}}
        {{--                },--}}
        {{--                error: function (response) {--}}
        {{--                    $('#btn-submit').attr('disabled', false);--}}
        {{--                    $('#btn-submit').text('Tải lên');--}}
        {{--                    $('#info').html('Có lỗi xảy ra').css('color', 'red');--}}
        {{--                }--}}
        {{--            })--}}
        {{--        }--}}
        {{--    });--}}

        {{--});--}}
    </script>
@endsection

