<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LockboxType;
use Carbon\Carbon;

class LockboxTypeController extends Controller
{
		public function createLockboxType(Request $request){
				$this->validate($request, [
						'name'  => 'required'
			  ]);

				$time = strtotime(Carbon::now());

    		$uuid = "lcbx".$time.rand(10,99)*rand(10,99);

				$lockbox_lype = new LockboxType;
				$lockbox_lype->uuid = $uuid;
				$lockbox_lype->name = $request->name;
				$save = $lockbox_lype->save();

				if ($save) {
						return $this->sendResponse("Lockbox type created successfully!");
				}else{
						return $this->sendResponse("Sorry, Something went wrong!", 200, false);
				}
		}

		public function updateLockboxType(Request $request){
				$this->validate($request, [
						'uuid'  => 'required',
						'name'  => 'required'
			  ]);

				$update = LockboxType::where('uuid', $request->uuid)->update(['name'=>$request->name]);

				if ($update) {
						return $this->sendResponse("Lockbox type updated successfully!");
				}else{
						return $this->sendResponse("Sorry, Something went wrong!", 200, false);
				}
		}

		public function getAllLockboxType(Request $request){
				$allLockboxType = LockboxType::get();

				if (sizeof($allLockboxType) > 0) {
						return $this->sendResponse($allLockboxType);
				}else{
						return $this->sendResponse("Sorry, Lockbox type not found!", 200, false);
				}
		}

		public function getSingleLockboxType(Request $request){
				$this->validate($request, [
						'uuid'  => 'required'
			  ]);

				$lockboxType = LockboxType::where('uuid', $request->uuid)->first();

				if (!empty($lockboxType)) {
						return $this->sendResponse($lockboxType);
				}else{
						return $this->sendResponse("Sorry, Lockbox type not found!", 200, false);
				}
		}

		public function deleteLockboxType(Request $request){
				$this->validate($request, [
						'uuid'  => 'required'
			  ]);

				$delete = LockboxType::where('uuid', $request->uuid)->delete();

				if ($delete) {
						return $this->sendResponse("Lockbox type deleted successfully!");
				}else{
						return $this->sendResponse("Sorry, Lockbox type not found!", 200, false);
				}
		}
}