@extends('master')
@section('title','Voter List')
@section('content')

<style>
    .border_box {
        background: #ccc !important;
        height: 20px;
        width: 20px;
        position: absolute;
        top: 4px;
        right: 4px;
    }

    .modal-window {
        position: fixed;
        background-color: rgb(9 33 82 / 39%);
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 999;
        visibility: hidden;
        opacity: 0;
        pointer-events: none;
        -webkit-transition: all 0.3s;
        transition: all 0.3s;
    }

    .modal-window-open {
        visibility: visible;
        opacity: 1;
        pointer-events: auto;
    }

    .modal-window>div {
        width: 300px;
        position: absolute;
        top: 50%;
        left: 50%;
        -webkit-transform: translate(-50%, -50%);
        transform: translate(-50%, -50%);
        padding: 14px 12px;
        background: #ffffff;
    }

    .titlemodel {
        font-size: 13px !important;

        border-bottom: 1px solid #CCC;
        padding: 0px 0px 9px;
        color: #3e3e3e;
        font-weight: 500;
    }

    .modal-window header {
        font-weight: bold;
    }

    .modal-window h1 {
        font-size: 150%;
        margin: 0 0 15px;
    }

    .modal-close {
        color: #aaa;
        line-height: 25px;
        font-size: 17px;
        position: absolute;
        right: 0;
        text-align: center;
        top: -16px;
        width: 20px;
        border-radius: 3px;
        text-decoration: none;
        background: #003165
    }

    .modal-close:hover {
        color: black;
    }

    /* Demo Styles */



    .modal-window div:not(:last-of-type) {
        margin-bottom: 15px;
    }

    small {
        color: #aaa;
    }

    .btn {
        background-color: #fff;
        padding: 1em 1.5em;
        border-radius: 3px;
        text-decoration: none;
    }

    .btn i {
        padding-right: 0.3em;
    }

    .cancel {
        color: white;
        background: #9a0000;
        border: 1px solid #9a0000;
        font-size: 12px;
        padding: 2px 7px;
        border-radius: 3px;
    }

    .savebtn {
        color: white;
        background: #003165;
        border: 1px solid #003165;
        font-size: 12px;
        padding: 2px 7px;
        border-radius: 3px;
    }

    .footerbtn {
        padding: 30px 2px 0px;
        float: right;
    }
</style>
<div class="maininnersection px-2 position-relative">
    <label for="ward_no" class="labelinput">Ward No - <span
            class="mx-1">{{str_pad($user->wards->ward_no,2,0,STR_PAD_LEFT)}}</span></label>
    <form method="get" action="{{route('report.voterlist')}}">
        <div class="w-100 mb-2 row mx-0">
            <div class="width-50">
                <select class="form-control selectinput" name="part_id" onchange="this.form.submit()">
                    <option value="">Select Part No </option>
                    @if (count($parts))
                    @foreach ($parts as $item)
                    <option value="{{$item->id}}" @if (isset($part_id)) @if ($part_id==$item->id) selected @endif
                        @endif>{{$item->part_no}}</option>
                    @endforeach
                    @endif
                </select>
            </div>
            <div class="width-50">
                <select class="form-control selectinput" name="color" onchange="this.form.submit()">
                    <option value="">All</option>
                    <option value="green" @if (isset($color)) @if ($color=='green' ) selected @endif @endif>Green
                    <option value="blue" @if (isset($color)) @if ($color=='blue' ) selected @endif @endif>Yellow
                    <option value="red" @if (isset($color)) @if ($color=='red' ) selected @endif @endif>Red
                    </option>
                </select>
            </div>
        </div>
    </form>
    <div id="refreshList">
        <div class="row mx-0">
            @if(count($eroDataWithC))
            @foreach ($eroDataWithC as $item)
            <div class="width-33">
                <div class="listimg">
                    <img src="{{$item->url}}" class="w-100 h-100" />
                    @if(isset($item->mobile))
                    <a href="tel:{{$item->mobile??''}}"><img class="calling-user"
                            src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZlcnNpb249IjEuMSIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHhtbG5zOnN2Z2pzPSJodHRwOi8vc3ZnanMuY29tL3N2Z2pzIiB3aWR0aD0iNTEyIiBoZWlnaHQ9IjUxMiIgeD0iMCIgeT0iMCIgdmlld0JveD0iMCAwIDUxMiA1MTIiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDUxMiA1MTIiIHhtbDpzcGFjZT0icHJlc2VydmUiIGNsYXNzPSIiPjxnPgo8cGF0aCB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHN0eWxlPSIiIGQ9Ik0yNTYsMEMxMTQuNjE3LDAsMCwxMTQuNjE3LDAsMjU2YzAsNTIuMDMsMTUuNTYzLDEwMC40MTQsNDIuMjMxLDE0MC44MThMMCw1MTJsMTE5LjEyOC0zOS43MDYgIEMxNTguNzIsNDk3LjM5OSwyMDUuNjM5LDUxMiwyNTYsNTEyYzE0MS4zODMsMCwyNTYtMTE0LjYxNywyNTYtMjU2UzM5Ny4zODMsMCwyNTYsMHoiIGZpbGw9IiMwMTI5NTQiIGRhdGEtb3JpZ2luYWw9IiMyNWFlODgiIGNsYXNzPSIiPjwvcGF0aD4KPHBhdGggeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiBzdHlsZT0iIiBkPSJNMzk3LjIzMywzMzUuMDc4VjM3Ny42YzAuMDYyLDE1LjY2LTEyLjYwNiwyOC4zOTgtMjguMjkyLDI4LjQ2OWMtMC44OTIsMC0xLjc4My0wLjAzNS0yLjY3NS0wLjExNSAgYy00My43MDUtNC43NC04NS42ODktMTkuNjQxLTEyMi41NzEtNDMuNTJjLTM0LjMxMy0yMS43Ni02My40MDktNTAuODAzLTg1LjIyMi04NS4wNTRjLTI0LjAwMi0zNi45NzktMzguOTM4LTc5LjA4Ni00My41OTktMTIyLjg5OCAgYy0xLjQxMi0xNS41OSwxMC4xMDgtMjkuMzc4LDI1LjczMi0zMC43OTFjMC44NDctMC4wNjIsMS42ODYtMC4xMDYsMi41MzQtMC4xMDZoNDIuNjExYzE0LjI1Ny0wLjE0MSwyNi40MTIsMTAuMjkzLDI4LjQwNywyNC4zODIgIGMxLjgwMSwxMy42MTIsNS4xMzgsMjYuOTY4LDkuOTQsMzkuODNjMy45MDIsMTAuMzY0LDEuNDA0LDIyLjA0Mi02LjM5MSwyOS45MDhsLTE4LjAzNSwxNy45OTkgIGMyMC4yMTUsMzUuNDg3LDQ5LjY2NCw2NC44NzQsODUuMjIxLDg1LjA1NGwxOC4wMzUtMTcuOTk5YzcuODgzLTcuNzg2LDE5LjU4OC0xMC4yNzUsMjkuOTctNi4zODIgIGMxMi44ODgsNC44MDIsMjYuMjcxLDguMTMsMzkuOTEsOS45MjJDMzg3LjA5LDMwOC4zMTIsMzk3LjU5NCwzMjAuNjg5LDM5Ny4yMzMsMzM1LjA3OHoiIGZpbGw9IiNmZmZmZmYiIGRhdGEtb3JpZ2luYWw9IiNmZmZmZmYiIGNsYXNzPSIiPjwvcGF0aD4KPGcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPC9nPgo8ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8L2c+CjxnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjwvZz4KPGcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPC9nPgo8ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8L2c+CjxnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjwvZz4KPGcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPC9nPgo8ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8L2c+CjxnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjwvZz4KPGcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPC9nPgo8ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8L2c+CjxnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjwvZz4KPGcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPC9nPgo8ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8L2c+CjxnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjwvZz4KPC9nPjwvc3ZnPg==" /></a>
                    @endif
                    @if ($item->color == 'green')
                    <div class="green_box" onclick="toggleColorModal(this);" data-wardid="{{$item->ward_id}}"
                        data-partid="{{$item->part_id}}" data-sno="{{$item->s_no}}" data-parshad="{{$user->id}}"
                        data-url="{{route('parshad.update.color')}}" data-color="green"></div>
                    @elseif ($item->color == 'blue')
                    <div class="yellow_box" onclick="toggleColorModal(this);" data-wardid="{{$item->ward_id}}"
                        data-partid="{{$item->part_id}}" data-sno="{{$item->s_no}}" data-parshad="{{$user->id}}"
                        data-url="{{route('parshad.update.color')}}" data-color="blue"></div>
                    @elseif ($item->color == 'red')
                    <div class="red_box" onclick="toggleColorModal(this);" data-wardid="{{$item->ward_id}}"
                        data-partid="{{$item->part_id}}" data-sno="{{$item->s_no}}" data-parshad="{{$user->id}}"
                        data-url="{{route('parshad.update.color')}}" data-color="red"></div>
                    @else
                    <a class="border_box" href="javascriptvoid:(0);" onclick="toggleColorModal(this);"
                        data-wardid="{{$item->ward_id}}" data-partid="{{$item->part_id}}" data-sno="{{$item->s_no}}"
                        data-parshad="{{$user->id}}" data-url="{{route('parshad.update.color')}}" data-color="grey"></a>
                    @endif
                </div>
            </div>
            @endforeach
            @elseif(count($eroDataWithoutC))
            @foreach ($eroDataWithoutC as $item)
            <?php
            $colorGet = App\Models\SurveyData::where(['parshad_id' => $user->id, 'ero_id' => $item->id])->first();
        ?>

            <div class="width-33">
                <div class="listimg">
                    <img src="{{$item->url}}" class="w-100 h-100" />
                    @if(isset($item->mobile))
                    <a href="tel:{{$item->mobile??''}}"><img class="calling-user"
                            src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZlcnNpb249IjEuMSIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHhtbG5zOnN2Z2pzPSJodHRwOi8vc3ZnanMuY29tL3N2Z2pzIiB3aWR0aD0iNTEyIiBoZWlnaHQ9IjUxMiIgeD0iMCIgeT0iMCIgdmlld0JveD0iMCAwIDUxMiA1MTIiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDUxMiA1MTIiIHhtbDpzcGFjZT0icHJlc2VydmUiIGNsYXNzPSIiPjxnPgo8cGF0aCB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHN0eWxlPSIiIGQ9Ik0yNTYsMEMxMTQuNjE3LDAsMCwxMTQuNjE3LDAsMjU2YzAsNTIuMDMsMTUuNTYzLDEwMC40MTQsNDIuMjMxLDE0MC44MThMMCw1MTJsMTE5LjEyOC0zOS43MDYgIEMxNTguNzIsNDk3LjM5OSwyMDUuNjM5LDUxMiwyNTYsNTEyYzE0MS4zODMsMCwyNTYtMTE0LjYxNywyNTYtMjU2UzM5Ny4zODMsMCwyNTYsMHoiIGZpbGw9IiMwMTI5NTQiIGRhdGEtb3JpZ2luYWw9IiMyNWFlODgiIGNsYXNzPSIiPjwvcGF0aD4KPHBhdGggeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiBzdHlsZT0iIiBkPSJNMzk3LjIzMywzMzUuMDc4VjM3Ny42YzAuMDYyLDE1LjY2LTEyLjYwNiwyOC4zOTgtMjguMjkyLDI4LjQ2OWMtMC44OTIsMC0xLjc4My0wLjAzNS0yLjY3NS0wLjExNSAgYy00My43MDUtNC43NC04NS42ODktMTkuNjQxLTEyMi41NzEtNDMuNTJjLTM0LjMxMy0yMS43Ni02My40MDktNTAuODAzLTg1LjIyMi04NS4wNTRjLTI0LjAwMi0zNi45NzktMzguOTM4LTc5LjA4Ni00My41OTktMTIyLjg5OCAgYy0xLjQxMi0xNS41OSwxMC4xMDgtMjkuMzc4LDI1LjczMi0zMC43OTFjMC44NDctMC4wNjIsMS42ODYtMC4xMDYsMi41MzQtMC4xMDZoNDIuNjExYzE0LjI1Ny0wLjE0MSwyNi40MTIsMTAuMjkzLDI4LjQwNywyNC4zODIgIGMxLjgwMSwxMy42MTIsNS4xMzgsMjYuOTY4LDkuOTQsMzkuODNjMy45MDIsMTAuMzY0LDEuNDA0LDIyLjA0Mi02LjM5MSwyOS45MDhsLTE4LjAzNSwxNy45OTkgIGMyMC4yMTUsMzUuNDg3LDQ5LjY2NCw2NC44NzQsODUuMjIxLDg1LjA1NGwxOC4wMzUtMTcuOTk5YzcuODgzLTcuNzg2LDE5LjU4OC0xMC4yNzUsMjkuOTctNi4zODIgIGMxMi44ODgsNC44MDIsMjYuMjcxLDguMTMsMzkuOTEsOS45MjJDMzg3LjA5LDMwOC4zMTIsMzk3LjU5NCwzMjAuNjg5LDM5Ny4yMzMsMzM1LjA3OHoiIGZpbGw9IiNmZmZmZmYiIGRhdGEtb3JpZ2luYWw9IiNmZmZmZmYiIGNsYXNzPSIiPjwvcGF0aD4KPGcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPC9nPgo8ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8L2c+CjxnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjwvZz4KPGcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPC9nPgo8ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8L2c+CjxnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjwvZz4KPGcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPC9nPgo8ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8L2c+CjxnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjwvZz4KPGcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPC9nPgo8ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8L2c+CjxnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjwvZz4KPGcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPC9nPgo8ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8L2c+CjxnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjwvZz4KPC9nPjwvc3ZnPg==" /></a>
                    @endif
                    @if (isset($colorGet) && $colorGet->red_green_blue == 'green')
                    <div class="green_box" onclick="toggleColorModal(this);" data-wardid="{{$item->ward_id}}"
                        data-partid="{{$item->part_id}}" data-sno="{{$item->s_no}}" data-parshad="{{$user->id}}"
                        data-url="{{route('parshad.update.color')}}" data-color="green"></div>
                    @elseif (isset($colorGet) && $colorGet->red_green_blue == 'blue')
                    <div class="yellow_box" onclick="toggleColorModal(this);" data-wardid="{{$item->ward_id}}"
                        data-partid="{{$item->part_id}}" data-sno="{{$item->s_no}}" data-parshad="{{$user->id}}"
                        data-url="{{route('parshad.update.color')}}" data-color="blue"></div>
                    @elseif (isset($colorGet) && $colorGet->red_green_blue == 'red')
                    <div class="red_box" onclick="toggleColorModal(this);" data-wardid="{{$item->ward_id}}"
                        data-partid="{{$item->part_id}}" data-sno="{{$item->s_no}}" data-parshad="{{$user->id}}"
                        data-url="{{route('parshad.update.color')}}" data-color="red"></div>
                    @else
                    <div class="border_box" data-wardid="{{$item->ward_id}}" data-partid="{{$item->part_id}}"
                        data-sno="{{$item->s_no}}" data-parshad="{{$user->id}}"
                        data-url="{{route('parshad.update.color')}}" data-color="grey"></div>
                    @endif
                </div>
            </div>
            @endforeach
            @else
            <h4>No data available</h4>
            @endif
        </div>
    </div>

    <div id="open-modal" class="modal-window">
        <div>
            <a href="#" title="Close" class="modal-close" onclick="toggleColorModal(this)">x</a>
            <h1 class="titlemodel">Choose Color </h1>
            <div class="row mb-2 d-inline mx-0">
                <input type="hidden" id="wardid" value="" />
                <input type="hidden" id="partid" value="" />
                <input type="hidden" id="sno" value="" />
                <input type="hidden" id="parshadid" value="" />
                <input type="hidden" id="current_color" value="" />
                <input type="hidden" id="url_this" value="" />
                <label class="container_radio_button">
                    <span class="greenbox"></span>
                    <input type="radio" id="green" class="checkedColor" name="red_green_blue" value="green">
                    <span class="checkmarkgreen checkmark"></span>
                </label>

                <label class="container_radio_button">
                    <span class="yellowbox"></span>
                    <input type="radio" id="blue" class="checkedColor" name="red_green_blue" value="blue">
                    <span class="checkmark"></span>
                </label>
                <label class="container_radio_button">
                    <span class="redbox"></span>
                    <input type="radio" id="red" class="checkedColor" name="red_green_blue" value="red">
                    <span class="checkmarkred  checkmark"></span>
                </label>
                <div class="footerbtn">

                    <button class="savebtn" onclick="updateColor(this);">OK</button>
                    <button class="cancel" onclick="toggleColorModal(this);">Cancel</button>
                </div>


            </div>


        </div>

    </div>

</div>
<script>
    function toggleColorModal(dis) {

    if ($(`.modal-window`).hasClass("modal-window-open")) {
        $(`.modal-window`).removeClass('modal-window-open');
    } else {
        let wardid = $(dis).data('wardid'),
            partid = $(dis).data('partid'),
            sno = $(dis).data('sno'),
            parshad = $(dis).data('parshad'),
            url = $(dis).data('url'),
            color = $(dis).data('color');
        $(`.modal-window`).addClass('modal-window-open');
        $(`#wardid`).val(wardid);
        $(`#partid`).val(partid);
        $(`#sno`).val(sno);
        $(`#parshadid`).val(parshad);
        $(`#current_color`).val(color);
        $(`#url_this`).val(url);
        $(`#${color}`).prop('checked', true);

    }
}
</script>
@endsection
