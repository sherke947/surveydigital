<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\State;
use App\Models\City;
use App\Models\EROData;
use App\Models\SurveyData;
use App\Models\NN;
use App\Models\NNNType;
use App\Models\Polling;
use App\Models\Section;
use App\Models\User;
use App\Models\Ward;
use App\Models\PartNo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Image;
use thiagoalessio\TesseractOCR\TesseractOCR;
use PDF;
use Illuminate\Support\Facades\Response;

class MasterController extends Controller
{
    function createAdmin(Request $request)
    {
        try {
            $ifExist = Admin::where('mobile', '1234567890')->first();
            if ($ifExist) {
                return ['success' => false, 'message' => 'Already exist'];
            }
            $admin = new Admin();
            $admin->type = 'admin';
            $admin->name = 'Admin';
            $admin->mobile = '1234567890';
            $admin->password = '123';
            $admin->save();
            return ['success' => true, 'message' => 'admin created'];
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    //Admin Parshad
    /** Login */
    function indexLoginAdminParshad(Request $request)
    {
        try {
            return view('Login.login');
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }
    /**Login Process */
    function adminLogin(Request $request)
    {
        try {
            $credentials = $request->only('mobile', 'password');
            if (Auth::guard('admin')->attempt($credentials, 1)) {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->back()->with('error', 'Wrong Mobile no or Password');
            }
        } catch (\Exception $exception) {
            return ['success' => false, 'message' => 'Server error', 'exception' => $exception->getMessage()];
        }
    }
    /**Admin Parshad Dashboard */
    function adminDashboard(Request $request)
    {
        try {
            $user = Auth::guard('admin')->user();
            if ($user->type == 'admin') {
                return view('Dashboard.admin');
            } else {
                return view('Dashboard.parshad');
            }
        } catch (\Exception $exception) {
            return ['success' => false, 'message' => 'Server error', 'exception' => $exception->getMessage()];
        }
    }
    /**Create parshad */
    function createParshad(Request $request)
    {
        try {
            $state = State::orderBy('state_name', 'asc')->get();
            $nnn_type = NNNType::orderBy('name', 'asc')->get();
            return view('Create.parshadRegister', compact('state', 'nnn_type'));
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }
    function createWardUser(Request $request)
    {
        try {
            $state = State::orderBy('state_name', 'asc')->get();
            $nnn_type = NNNType::orderBy('name', 'asc')->get();
            return view('Create.wardUser', compact('state', 'nnn_type'));
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }
    /**Store parshad */
    function storeParshad(Request $request)
    {
        try {

            $validation = Validator::make($request->all(), [

                'name' => ['required'],
                'mobile' => ['required', 'digits:10'],
                // 'email' => ['', 'email'],
                'city' => ['required'],
                'nnn_id' => ['required'],
                'nn_id' => ['required'],
                'ward_id' => ['required'],

            ]);
            if ($validation->fails()) {
                return back()->withErrors($validation)->withInput();
            }
            $input = $request->all();
            $ifExistMobile = User::where('mobile', $input['mobile'])->where('id', '!=', $input['id'])->whereNull('deleted_at')->first();
            $ifExistEmail = User::where('email', $input['email'])->where('id', '!=', $input['id'])->whereNull('deleted_at')->first();
            if ($ifExistMobile) {
                return back()->with('error', 'Mobile number allready exist')->withInput();
            }
            if ($ifExistEmail) {
                return back()->with('error', 'Email already exist')->withInput();
            }
            $input['password'] = Hash::make($request->password);
            $input['password2'] = $request->password;
            $input['type'] = 'parshad';
            if ($input['id']) {
                User::find($input['id'])->update($input);
                return redirect()->route('admin.list.parshad')->with('success', 'Update successfully');
            } else {

                User::create($input);
                return back()->with('success', 'Saved successfully');
            }

            return view('parshadRegister', compact('state'));
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }
    function storeWardUser(Request $request)
    {
        try {

            $validation = Validator::make($request->all(), [

                'name' => ['required'],
                'mobile' => ['required', 'digits:10'],
                // 'email' => ['', 'email'],
                'city' => ['required'],
                'nnn_id' => ['required'],
                'nn_id' => ['required'],
                'ward_id' => ['required'],

            ]);
            if ($validation->fails()) {
                return back()->withErrors($validation)->withInput();
            }
            $input = $request->all();
            $ifExistMobile = User::where('mobile', $input['mobile'])->where('id', '!=', $input['id'])->whereNull('deleted_at')->first();
            $ifExistEmail = User::where('email', $input['email'])->where('id', '!=', $input['id'])->whereNull('deleted_at')->first();
            if ($ifExistMobile) {
                return back()->with('error', 'Mobile number allready exist')->withInput();
            }
            if ($ifExistEmail) {
                return back()->with('error', 'Email already exist')->withInput();
            }
            $input['password'] = Hash::make($request->password);
            $input['password2'] = $request->password;
            $input['type'] = 'warduser';
            if ($input['id']) {
                User::find($input['id'])->update($input);
                return redirect()->route('admin.list.warduser')->with('success', 'Update successfully');
            } else {

                User::create($input);
                return back()->with('success', 'Saved successfully');
            }

            // return view('parshadRegister', compact('state'));
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }
    /**List parshad */
    function parshadList(Request $request)
    {
        try {
            $parshad = User::where(['type' => 'parshad'])->whereNull('deleted_at')->orderBy('id', 'desc')->get();
            return view('List.parshad', compact('parshad'));
        } catch (\Exception $exception) {
            return ['success' => false, 'message' => 'Server error', 'exception' => $exception->getMessage()];
        }
    }
    /**List parshad */
    function wardUserList(Request $request)
    {
        try {
            $parshad = User::where(['type' => 'warduser'])->whereNull('deleted_at')->orderBy('id', 'desc')->get();
            return view('List.wardUser', compact('parshad'));
        } catch (\Exception $exception) {
            return ['success' => false, 'message' => 'Server error', 'exception' => $exception->getMessage()];
        }
    }
    /**Edit parshad */
    function parshadEdit(Request $request, $id)
    {
        try {
            $state = State::orderBy('state_name', 'asc')->get();
            $editParshad = User::find(base64_decode($id));
            return view('Edit.parshad', compact('editParshad', 'state'));
        } catch (\Exception $exception) {
            return ['success' => false, 'message' => 'Server error', 'exception' => $exception->getMessage()];
        }
    }
    /**Edit Ward user */
    function wardUserEdit(Request $request, $id)
    {
        try {
            $state = State::orderBy('state_name', 'asc')->get();
            $editWardUser = User::find(base64_decode($id));
            return view('Edit.wardUser', compact('editWardUser', 'state'));
        } catch (\Exception $exception) {
            return ['success' => false, 'message' => 'Server error', 'exception' => $exception->getMessage()];
        }
    }
    /**Delete parshad */
    function parshadDelete(Request $request, $id)
    {
        try {
            User::find(base64_decode($id))->update(['deleted_at' => Carbon::now()]);
            return ['success' => true, 'message' => "Deleted successfully"];
        } catch (\Exception $exception) {
            return ['success' => false, 'message' => 'Server error', 'exception' => $exception->getMessage()];
        }
    }
    function parshadReportVoterList(Request $request, $id)
    {
        try {
            $user = User::find(base64_decode($id));
            $ward_id = $user->ward_id;
            $parts = $user->wards->part_nos;
            return view('Report.Admin.voterlist', compact('user', 'ward_id', 'parts'));
            return ['success' => true, 'message' => "Deleted successfully"];
        } catch (\Exception $exception) {
            return ['success' => false, 'message' => 'Server error', 'exception' => $exception->getMessage()];
        }
    }

    function reportVoterlist(Request $request)
    {
        try {
            //  return $request->all();
            $id = base64_decode($request->parshad_id);
            $user = User::find($id);
            $ward_id = $user->ward_id;
            $part_id = $request->part_id;
            $color = $request->color;
            $parts = $user->wards->part_nos;
            $eroDataWithC = [];
            $eroDataWithoutC = [];
            if ($part_id && !$color) {
                $eroDataWithoutC = EROData::where(['ward_id' => $ward_id, 'part_id' => $part_id])->orderBy('s_no', 'asc')->get();
            } else {
                $eroDataWithC = EROData::join('survey_data', 'e_r_o_data.id', '=', 'survey_data.ero_id')
                    ->where(['e_r_o_data.ward_id' => $ward_id, 'e_r_o_data.part_id' => $part_id])
                    ->when(request('color'), function ($q) use ($user) {
                        return $q->where(['parshad_id' => $user->id, 'survey_data.red_green_blue' => request('color')]);
                    })
                    ->select('e_r_o_data.*', 'survey_data.red_green_blue as color')
                    ->orderBy('e_r_o_data.s_no', 'asc')->get();
            }
            $data = [
                'user' => $user,
                'eroDataWithC' => $eroDataWithoutC,
                'eroDataWithoutC' => $eroDataWithC,
                'part_id' => $part_id,
                'color' => $color
            ];
            // return view('Report.Admin.voterlistPDF', compact('user', 'eroDataWithC', 'eroDataWithoutC', 'part_id', 'color'));

            $veiw = view('Report.Admin.voterlistPDF', compact('user', 'eroDataWithC', 'eroDataWithoutC', 'part_id', 'color'))->render();
            // $pdf = PDF::loadView('Report.Admin.voterlistPDF',$data);
            // return $pdf->download('medium.pdf');

            $apikey = '32182451-d37f-4a50-a14a-23b26ca1cd1c';
            $value = $veiw;

            $postdata = http_build_query(
                array(
                    'apikey' => $apikey,
                    'value' => $value,
                    'MarginBottom' => '30',
                    'MarginTop' => '20'
                )
            );

            $opts = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postdata
            ));

            $context  = stream_context_create($opts);

            // Convert the HTML string to a PDF using those parameters
            $result = file_get_contents('http://api.html2pdfrocket.com/pdf', false, $context);

            // Save to root folder in website
            return file_put_contents('mypdf-1.pdf', $result);

            $headers = array(
                'Content-Type: application/pdf',
            );
            // $file = file_get_contents(url('mypdf-1.pdf'));
            // return Response::download($_SERVER['DOCUMENT_ROOT'] . '/' . 'election_survey/' . 'mypdf-1.pdf', 'filename.pdf', $headers);
            return Response::download($result, 'filename.pdf', $headers);

            return view('Report.Admin.voterlistPDF', compact('user', 'eroDataWithC', 'eroDataWithoutC', 'part_id', 'color'));
        } catch (\Exception $exception) {
            return ['success' => false, 'message' => 'Server error', 'exception' => $exception->getMessage()];
        }
    }
    /**Delete parshad */
    function wardUserDelete(Request $request, $id)
    {
        try {
            User::find(base64_decode($id))->update(['deleted_at' => Carbon::now()]);
            return ['success' => true, 'message' => "Deleted successfully"];
        } catch (\Exception $exception) {
            return ['success' => false, 'message' => 'Server error', 'exception' => $exception->getMessage()];
        }
    }
    /**Get City */
    function getCity(Request $request)
    {
        try {
            $state_id = $request->state_id;
            $oldCity = $request->oldCity;
            $cities = City::where('state_id', $state_id)->orderBy('city_name', 'asc')->get();
            $view = view('Component.cities', compact('cities', 'oldCity'))->render();
            return ['success' => true, 'view' => $view];
        } catch (\Exception $exception) {
            return ['success' => false, 'message' => 'Server error', 'exception' => $exception->getMessage()];
        }
    }
    /**Get NN */
    function getNN(Request $request)
    {
        try {
            $id = $request->id;
            $id2 = $request->id2;
            $oldId = $request->oldId;
            $nn = NN::where(['nnn_id' => $id, 'city_id' => $id2])->orderBy('name', 'asc')->get();
            $view = view('Component.nn', compact('nn', 'oldId'))->render();
            return ['success' => true, 'view' => $view];
        } catch (\Exception $exception) {
            return ['success' => false, 'message' => 'Server error', 'exception' => $exception->getMessage()];
        }
    }
    /**Get Ward */
    function getWard(Request $request)
    {
        try {
            $id = $request->id;
            $oldId = $request->oldId;
            $ward = Ward::where('nn_id', $id)->orderBy('ward_no', 'asc')->get();
            $view = view('Component.ward', compact('ward', 'oldId'))->render();
            return ['success' => true, 'view' => $view];
        } catch (\Exception $exception) {
            return ['success' => false, 'message' => 'Server error', 'exception' => $exception->getMessage()];
        }
    }
    /**Get Section */
    function getSection(Request $request)
    {
        try {
            $id = explode(",", $request->id)[0];
            $ward_id = $request->ward_id;
            $oldId = $request->oldId;
            $sections = Section::where(['ward_id' => $ward_id, 'part_id' => $id])->orderBy('section_no', 'asc')->get();
            $view = view('Component.sections', compact('sections', 'oldId'))->render();
            return ['success' => true, 'view' => $view];
        } catch (\Exception $exception) {
            return ['success' => false, 'message' => 'Server error', 'exception' => $exception->getMessage()];
        }
    }
    /**Get part */
    function getPart(Request $request)
    {
        try {
            $id = $request->id;
            $oldId = $request->oldId;
            $part_nos = PartNo::where('ward_id', $id)->orderBy('part_no', 'asc')->get();
            $view = view('Component.part_nos', compact('part_nos', 'oldId'))->render();
            return ['success' => true, 'view' => $view];
        } catch (\Exception $exception) {
            return ['success' => false, 'message' => 'Server error', 'exception' => $exception->getMessage()];
        }
    }
    /**Get Section data */
    function getSectionData(Request $request)
    {
        try {
            $id = $request->id;
            $oldId = $request->oldId;
            $totalAllotted = 0;
            $sectionData = Section::find($id);
            $totalVoter = Section::where(['ward_id' => $sectionData->ward_id, 'part_id' => $sectionData->part_id, ['section_no', '<', $sectionData->section_no]])->sum('total_voter');
            $startingVoter = $totalVoter + 1;
            $lastVoter = $totalVoter  + $sectionData->total_voter;
            $allotted = User::where('section_id', $id)->get();
            if (count($allotted)) {
                foreach ($allotted as $key => $value) {
                    $currentCount = $value->s_no_to - $value->s_no_from + 1;
                    $totalAllotted = $totalAllotted + $currentCount;
                }
            }
            return ['success' => true, 'data' => $sectionData, 'totalAllotted' => $totalAllotted, 'startingVoter' => $startingVoter, 'lastVoter' => $lastVoter];
        } catch (\Exception $exception) {
            return ['success' => false, 'message' => 'Server error', 'exception' => $exception->getMessage()];
        }
    }
    function uploadImageIndex(Request $request)
    {
        try {
            $state = State::orderBy('state_name', 'asc')->get();
            $nnn_type = NNNType::orderBy('name', 'asc')->get();
            return view('uploadImages', compact('state', 'nnn_type'));
        } catch (\Exception $exception) {
        }
    }
    function uploadImage(Request $request)
    {
        try {
            // return $request->all();

            $originalImage = $request->file('image');
            $brackPoints = explode(",", $request->breakPoints);
            $city_id = $request->city;
            $nnn_id = $request->nnn_id;
            $nn_id = $request->nn_id;
            $ward_id = $request->ward_id;
            $part_id = $request->part_id;
            $s_no = 0;
            $ward_no = Ward::find($ward_id)->ward_no ?? $ward_id;
            $part_no = PartNo::find($part_id)->part_no ?? $part_id;
            $ward_dir = 'W' . str_pad($ward_no, 3, 0, STR_PAD_LEFT);
            $part_dir = 'P' . str_pad($part_no, 3, 0, STR_PAD_LEFT);
            if (!file_exists($ward_dir)) {
                mkdir($ward_dir, 0777, true);
            }
            if (!file_exists($ward_dir . '/' . $part_dir)) {
                mkdir($ward_dir . '/' . $part_dir, 0777, true);
            }
            foreach ($originalImage as $index1 => $list) {
                $main_crop_name = $ward_dir . '/' . $ward_dir . str_pad($index1 + 1, 3, 0, STR_PAD_LEFT) . '.' . $originalImage[$index1]->getClientOriginalExtension();
                if (file_exists($main_crop_name)) {
                    unlink($main_crop_name);
                }
                $mainCropImage1 = Image::make($originalImage[$index1]);
                // $mainCropImage1->crop(1127, 1512, 59, 142);
                $mainCropImage1->crop(1507, 2015, 79, 189);
                $mainCropImage1->save($main_crop_name);
                $data = file_get_contents($main_crop_name);
                $initWidth = 480;
                $initHeight = 200;
                $initX = 0;
                $initY = 0;
                $rows = 1;
                $blockNo = 1;
                $blank = true;
                for ($i = 1; $i <= 30; $i++) {
                    if ($blank) {
                        $s_no = $s_no + 1;
                        $mainCropImage = Image::make($data);
                        $mainCropImage->crop($initWidth, $initHeight, $initX, $initY);
                        // $type = $request->file('image')->getClientOriginalExtension();
                        // $data = file_get_contents($mainCropImage->dirname . '/' . $mainCropImage->basename);

                        // $paath = $cropBlogPath . time() . '_' . $i . $originalImage[$index1]->getClientOriginalName();
                        // $mainCropImage->save($paath);
                        // $response2 = (new TesseractOCR($paath))
                        //     ->lang('hin', 'eng')
                        //     ->executable('C:\Program Files\Tesseract-OCR/tesseract.exe')
                        //     ->run();
                        $response2 = true;
                        if (in_array($s_no, $brackPoints)) {
                            $blank = false;
                        }
                        if ($response2) {

                            $save_path = $ward_dir . '/' . $part_dir . '/' . $ward_dir . $part_dir . str_pad($s_no, 3, 0, STR_PAD_LEFT) . '.' . $originalImage[$index1]->getClientOriginalExtension();
                            if (file_exists($save_path)) {
                                unlink($save_path);
                            }
                            $mainCropImage->save($save_path);
                            $saveData = EROData::where(['part_id' => $part_id, 'ward_id' => $ward_id, 's_no' => $s_no])->first();
                            if (!$saveData) {
                                $saveData = new EROData();
                            }
                            $saveData->nn_id = $nn_id;
                            $saveData->nnn_id = $nnn_id;
                            $saveData->city_id = $city_id;
                            $saveData->ward_id = $ward_id;
                            $saveData->part_id = $part_id;
                            $saveData->s_no = $s_no;
                            $saveData->path = $save_path;
                            $saveData->save();
                            $mod = fmod($i, 3);
                            if ($mod == 0) {
                                $rows = $rows + 1;
                                $blockNo = 1;
                            } else {
                                $blockNo = $blockNo + 1;
                            }
                            if ($rows == 1) {
                                if ($blockNo == 1) {
                                    $initX = 0;
                                } else {
                                    $initX = $initX + 500;
                                }
                            }
                            if ($rows == 2) {
                                if ($blockNo == 1) {
                                    $initX = 0;
                                    $initY = $initY + 200;
                                } else {
                                    $initX = $initX + 500;
                                }
                            }
                            if ($rows == 3) {
                                if ($blockNo == 1) {
                                    $initX = 0;
                                    $initY = $initY + 205;
                                } else {
                                    $initX = $initX + 500;
                                }
                            }
                            if ($rows == 4) {
                                if ($blockNo == 1) {
                                    $initX = 0;
                                    $initY = $initY + 200;
                                } else {
                                    $initX = $initX + 500;
                                }
                            }
                            if ($rows == 5) {
                                if ($blockNo == 1) {
                                    $initX = 0;
                                    $initY = $initY + 200;
                                } else {
                                    $initX = $initX + 500;
                                }
                            }
                            if ($rows == 6) {
                                if ($blockNo == 1) {
                                    $initX = 0;
                                    $initY = $initY + 200;
                                } else {
                                    $initX = $initX + 500;
                                }
                            }
                            if ($rows == 7) {
                                if ($blockNo == 1) {
                                    $initX = 0;
                                    $initY = $initY + 205;
                                } else {
                                    $initX = $initX + 500;
                                }
                            }
                            if ($rows == 8) {
                                if ($blockNo == 1) {
                                    $initX = 0;
                                    $initY = $initY + 200;
                                } else {
                                    $initX = $initX + 500;
                                }
                            }
                            if ($rows == 9) {
                                if ($blockNo == 1) {
                                    $initX = 0;
                                    $initY = $initY + 200;
                                } else {
                                    $initX = $initX + 500;
                                }
                            }
                            if ($rows == 10) {
                                if ($blockNo == 1) {
                                    $initX = 0;
                                    $initY = $initY + 200;
                                } else {
                                    $initX = $initX + 500;
                                }
                            }
                        }
                    }
                }
            }
            return ['success' => true, 'message' => 'Image(s) uploaded'];
            return back()->with('success', 'Saved successfully');
        } catch (\Exception $exception) {
            return ['success' => false, 'exception' => $exception->getMessage()];
        }
    }


    /**Get Polling booth */
    function getPolling(Request $request)
    {
        try {
            $user = Auth::user();
            $ward = $user->wards->ward_no;
            $part_id = explode(",", $request->id)[0];
            $part_no = explode(",", $request->id)[1];
            $oldId = $request->oldId;
            $pollings = Polling::where(['ward_id' => $ward, 'part_id' => $part_no])->orderBy('polling_no', 'asc')->get();
            $view = view('Component.pollings', compact('pollings', 'oldId'))->render();
            return ['success' => true, 'view' => $view];
        } catch (\Exception $exception) {
            return ['success' => false, 'message' => 'Server error', 'exception' => $exception->getMessage()];
        }
    }
    /**Logout Admin Parshad */
    function adminLogout(Request $request)
    {
        try {
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login')->with('success', 'You have successfully loged out');
        } catch (\Exception $exception) {
            return ['success' => false, 'message' => 'Server error', 'exception' => $exception->getMessage()];
        }
    }
}
