@extends('master')
@section('title','Parshad Dashboard')
@section('content')
<style>
.width-40 {
    width: 28%;
    font-size: 32px;
    padding: 10px 3px;
    color: #ffffff;

    background: #012954;
}
.width-60 {
      width: 72%;
    padding: 18px 0px;
    color: #060606;
    font-size: 15px;
    font-weight: 500;
    text-align: left;
    padding-left: 9px;
}
.serverdashboard {
    width: 100%;
    border: 1px dashed #ccc;
    margin: 8px auto 0px !important;
}
</style>
<div class="maindiv">
    <!-- Default form register -->
    <form class="text-center" action="#!">
        <p class="h5 mb-3">Ward No -
            {{str_pad(Auth::user()->wards->ward_no,2,0,STR_PAD_LEFT)??'N/A'}}
            ({{Auth::user()->wards->ward_name??'N/A'}})
        </p>

        <div class="form-row mb-4">
            <!-- E-mail -->
            <a href="{{route('parshad.create.surveyor')}}" class="w-100 serverdashboard"  class="">

             <div class="row mx-0">
            <div class="width-40">
           <i class="fas fa-user-friends"></i>
            </div>
            <div class="width-60">
             <div class="titlemenu">
                   Create Survey User
                </div>
            </div>
            </div>
             </a>
            <a href="{{route('parshad.list.surveyor')}}"  class="w-100 serverdashboard" >

              <div class="row mx-0">
            <div class="width-40">
      <i class="fas fa-list"></i>
            </div>
            <div class="width-60">
             <div class="titlemenu">
                    List Survey User
                </div>
            </div>
            </div>
             </a>
            <a href="{{route('parshad.create.booth.agent')}}" class="w-100 serverdashboard" >
               <div class="row mx-0">
            <div class="width-40">
          <i class="fas fa-vote-yea"></i>
            </div>
            <div class="width-60">
             <div class="titlemenu">
                  Create Poling Booth Agent
                </div>
            </div>
            </div>

            </a>
            <a href="{{route('parshad.list.booth.agent')}}"  class="w-100 serverdashboard" >
              <div class="row mx-0">
            <div class="width-40">
               <i class="fas fa-list"></i>
            </div>
            <div class="width-60">
             <div class="titlemenu">
                   List Poling Booth Agent
                </div>
            </div>
            </div>

             </a>
            <a href="" class="btn btn-wa">Reports</a>
            <!-- <a href="{{route('logout')}}" class="btn btn-wa">Logout</a> -->




            <!-- Terms of service -->
    </form>

    <!-- Default form register -->
</div>
@endsection
