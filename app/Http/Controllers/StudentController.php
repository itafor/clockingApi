<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use App\Student;
use App\Clock;

class StudentController extends Controller
{
  
    public function createStudents(Request $request){
    	 $newStudent = new Student();
       $newStudent->name  = $request->name;
       $newStudent->gender  = $request->gender;
       $newStudent->email  = $request->email;
       $newStudent->save();
       if($newStudent){
       	return $newStudent;
       } 
        return response()->json([
    'error' => 'Student could not be created at this time, try again',
],400);

    }

    public function fetchStudent($id){
      $student = Student::join('clocks','students.id','=','clocks.studentId')
      ->selectRaw('students.name as fullName, students.gender as Gender,students.email as Email,
      clocks.timeIn as TimeIn, clocks.timeOut as TimeOut,clocks.status as Status,clocks.id')
  ->where('students.id',$id)->first();
       if(!$student){
        return response()->json(
          ['error'=>'We cannot find your record in our database!!!'],401);
        }
        return  response()->json([
          'Full name'=> $student->fullName,
          'Gender'=> $student->Gender,
          'Email'=> $student->Email,
          'Time In'=> $student->TimeIn,
          'Time Out'=> $student->TimeOut,
          'Status'=> $student->Status == 1 ? 'Clocked' : 'Unclocked',
        ],200);
    }


    public function clockStudent($id){
     
            $checkStudent =Student::where('id',$id)->first();
       if(!$checkStudent){
        return response()->json(
          ['error'=>'We cannot find your record in our database!!!'],401);
        }
       
       
        $getClockedStudent = $this->getStudentById($id) ;
      //  var_dump($getClockedStudent);
      $dateDiff = '';
        if($getClockedStudent){

          $timeIN=Carbon::parse($getClockedStudent->timeIn);
          $now = Carbon::now();
           $this->$dateDiff=$now->diffInDays($timeIN);
            // var_dump($dateDiff);
          if($dateDiff > 1){
            $this->clockMe($id);
            $student = $this->getClockedInStudent($id);
            return  response()->json([
             'success'=>'You have successfully been clocked',
             'Full name'=> $student->fullName,
             'Gender'=> $student->Gender,
             'Email'=> $student->Email,
             'Time In'=> $student->TimeIn,
             'Status'=> $student->Status == 1 ? 'Clocked' : 'Unclocked',
           ],200);
          }
                 return response()->json([
        'warning' => 'Sorry you can\'t clock twice per day, You have been clocked for today, Thanks!!',
    ],401);
      
    }
    if($getClockedStudent === null && $dateDiff < 1){
      var_dump($dateDiff);
        $this->clockMe($id);
        $student = $this->getClockedInStudent($id);
        return  response()->json([
         'success'=>'You have successfully been clocked',
         'Full name'=> $student->fullName,
         'Gender'=> $student->Gender,
         'Email'=> $student->Email,
         'Time In'=> $student->TimeIn,
         'Status'=> $student->Status == 1 ? 'Clocked' : 'Unclocked',
       ],200);
      }
       
    
}

    public function clockMe($studentid){
      $clockStudent = new Clock();
      $clockStudent->timeIn  = Carbon::now();
      $clockStudent->timeOut  = null;
      $clockStudent->status  =  1;
      $clockStudent->studentId  = $studentid;
      $clockStudent->save();
    }
  
    public function UnclockStudent($id){
            $checkStudent =Student::where('id',$id)->first();
       if(!$checkStudent){
        return response()->json(
          ['error'=>'We cannot find your record in our database!!!'],403);
        }

          $checkUnClockedStudent=Clock::where('studentId',$id)
          ->where('status',1)
          ->Where('timeOut',null)
          ->orderBy('timeIn','DESC')->first();
          

          if($checkUnClockedStudent){
          $updateclocks = Clock::where('studentId', $id)
          ->update([
            'timeOut' => Carbon::now(),
            'status' => 0,
          ]);

              $student = $this->getClockedInStudent($id);
              return  response()->json([
               'success'=>'You have successfully been unclocked',
               'Full name'=> $student->fullName,
               'Gender'=> $student->Gender,
               'Email'=> $student->Email,
               'Time In'=> $student->TimeIn,
               'Time Out'=> $student->TimeOut,
               'Status'=> $student->Status == 1 ? 'Clocked' : 'Unclocked',
             ],200);
        }
        return response()->json([
          'warning' => 'You already  been clocked out for today, Thanks!!',
      ],401);
    }


    public function getClockedInStudent($id){
      $student = Student::join('clocks','students.id','=','clocks.studentId')
      ->selectRaw('students.name as fullName, students.gender as Gender,students.email as Email,
      clocks.timeIn as TimeIn, clocks.timeOut as TimeOut,clocks.status as Status,clocks.id')
  ->where('clocks.studentId',$id)->first();
 return $student;
    }

    public function getStudentById($id){
      $checkUnClockedStudent=Clock::where('studentId',$id)
          ->where('status',1)
          ->where('timeOut',null)
          ->orderBy('timeIn','DESC')->first();
          return $checkUnClockedStudent;
    }
    public function checkForClockedStudent($id){
      $checkClockedStudent=Clock::where('studentId',$id);
      return $checkClockedStudent;
    }
 public  function getDetailOfStudent($id){
      $std=Clock::where('studentId',$id)->get();
      return $std;
    }

    public function allStudents(){
      $student=Student::join('clocks','students.id','=','clocks.studentId')
      ->selectRaw('students.name as fullName, students.gender as Gender,students.email as Email,
      clocks.timeIn as TimeIn, clocks.timeOut as TimeOut,clocks.status as Status')
      ->orderBy('students.id','asc')->get();
      return $student;
    }


    public function clockedStudents(){
      $clockedStudent=Student::join('clocks','students.id','=','clocks.studentId')
      ->selectRaw('students.name as fullName, students.gender as Gender,students.email as Email,
      clocks.timeIn as TimeIn, clocks.timeOut as TimeOut,clocks.status as Status')
      ->where('clocks.timeOut',null)
      ->orderBy('clocks.timeIn','asc')->get();
      return $clockedStudent;
    }
    
    public function allunclockedStudents(){
      $unclockedStudent=Student::join('clocks','students.id','=','clocks.studentId')
      ->selectRaw('students.name as fullName, students.gender as Gender,students.email as Email,
      clocks.timeIn as TimeIn, clocks.timeOut as TimeOut,clocks.status as Status')
      ->where('clocks.status',0)
      ->whereNotNull('clocks.timeOut')
      ->orderBy('clocks.timeOut','asc')->get();
      return $unclockedStudent;
    }
}
