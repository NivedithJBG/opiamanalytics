<?php 

use app\models\Projects;
use app\models\ProjuserSelection;

$selProjectName = '';
if($selProject = Projects::findOne($seleted_project_id))
  $selProjectName = $selProject->Name;



?>



<main class="p-6">

    <!-- Page Title Start -->
    <div class="flex justify-between items-center mb-6">
        <h4 class="text-slate-900 text-2xl font-medium"><?php echo $selProjectName; ?></h4>
    </div>
    <!-- Page Title End -->


     <!-- Tab Start -->
    <div class="flex flex-col gap-6">
        <div>
            <div data-fc-type="tab">
                <nav class="flex space-x-2 border-b border-gray-200" aria-label="Tabs" role="tablist">
                    <button data-fc-target="#card-type-tab-1" type="button" class="fc-tab-active:bg-white fc-tab-active:border-b-transparent fc-tab-active:text-primary  -mb-px py-3 px-4 inline-flex items-center gap-2 bg-gray-50 text-base font-bold text-center border text-gray-500 rounded-t-lg hover:text-gray-700 active" id="card-type-tab-item-1" aria-controls="card-type-tab-1" role="tab">
                       <span class="ph ph-toolbox text-2xl font-medium"></span> <span class="md:flex hidden">Project</span>
                    </button>
                   <!--  <button data-fc-target="#card-type-tab-2" type="button" class="fc-tab-active:bg-white fc-tab-active:border-b-transparent fc-tab-active:text-primary  -mb-px py-3 px-4 inline-flex items-center gap-2 bg-gray-50 text-base font-bold text-center border text-gray-500 rounded-t-lg hover:text-gray-700" id="card-type-tab-item-2" aria-controls="card-type-tab-2" role="tab">
                        <span class="ph ph-calendar-check text-2xl font-medium"></span> <span class="md:flex hidden">Project Schedule</span>
                    </button> -->
                    <button data-fc-target="#card-type-tab-3" type="button" class="fc-tab-active:bg-white fc-tab-active:border-b-transparent fc-tab-active:text-primary  -mb-px py-3 px-4 inline-flex items-center gap-2 bg-gray-50 text-base font-bold text-center border text-gray-500 rounded-t-lg hover:text-gray-700 activityTab" id="card-type-tab-item-3" aria-controls="card-type-tab-3" role="tab">
                        <span class="ph ph-notepad text-2xl font-medium"></span><span class="md:flex hidden">Reporting</span>
                    </button>
                </nav>

                <div class="p-6" style="background-color: white;">
                    
                     <!-- Tab 1 : Project -->

                    <div id="card-type-tab-1" role="tabpanel" aria-labelledby="card-type-tab-item-1">
                        <div class="project-card">
                            <!-- <div class="card-header flex justify-end items-center mb-6">
                                <a href="javascript:void(0);" onclick="onProjectListClick()" class="btn btn-sm bg-light !text-sm text-gray-800 mr-2">List</a>
                                <a href="javascript:void(0);" data-fc-type="modal" class="btn btn-sm bg-light !text-sm text-gray-800 ">Add</a>
                                
                                <div class="hidden w-full h-full fixed top-0 start-0 z-50 overflow-x-hidden overflow-y-auto">
                                    <div class="fc-modal-open:mt-7 fc-modal-open:opacity-100 fc-modal-open:duration-500 mt-0 opacity-0 ease-out transition-all lg:max-w-4xl lg:w-full m-3 lg:mx-auto">
                                        <div class="flex flex-col bg-white border shadow-sm rounded-xl">
                                            <div class="flex justify-between items-center py-2.5 px-4 border-b">
                                                <h3 class="font-bold text-gray-800">
                                                    Add Project
                                                </h3>
                                                <button data-fc-dismiss type="button" class="inline-flex flex-shrink-0 justify-center items-center h-8 w-8 rounded-md text-gray-500 hover:text-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 focus:ring-offset-white transition-all text-sm">
                                                    <span class="sr-only">Close</span>
                                                    <i class="bx bx-x text-xl"></i>
                                                </button>
                                            </div>
                                            <div class="p-4 overflow-y-auto">
                                                <div class="grid lg:grid-cols-3 gap-6">

                                                    <div>
                                                        <label for="simpleinput" class="text-gray-800 text-sm font-medium inline-block mb-2">Project Name</label>
                                                        <input type="text" id="simpleinput" class="form-input">
                                                    </div>
                    
                                                    <div>
                                                        <label for="example-email" class="text-gray-800 text-sm font-medium inline-block mb-2">Duration</label>
                                                        <input type="email" id="example-email" name="example-email" class="form-input">
                                                    </div>
                    
                                                    <div class="grid lg:grid-cols-2 gap-6">
                                                        <div>
                                                            <label for="example-date" class="text-gray-800 text-sm font-medium inline-block mb-2">Start Date</label>
                                                            <input class="form-input" id="example-date" type="date" name="date">
                                                        </div>
                                                        <div>
                                                            <label for="example-date" class="text-gray-800 text-sm font-medium inline-block mb-2">End Date</label>
                                                            <input class="form-input" id="example-date" type="date" name="date">
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label for="simpleinput" class="text-gray-800 text-sm font-medium inline-block mb-2">Project Value</label>
                                                        <input type="text" id="simpleinput" class="form-input">
                                                    </div>
                    
                                                    <div>
                                                        <label for="example-email" class="text-gray-800 text-sm font-medium inline-block mb-2">Client Name</label>
                                                        <input type="email" id="example-email" name="example-email" class="form-input">
                                                    </div>
                                                    <div>
                                                        <label for="example-email" class="text-gray-800 text-sm font-medium inline-block mb-2">Work Hours</label>
                                                        <input type="email" id="example-email" name="example-email" class="form-input">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex justify-end items-center gap-x-2 py-2.5 px-4 border-t">
                                                <button data-fc-dismiss type="button" class="btn bg-secondary text-white">
                                                    Close
                                                </button>
                                                <a class="btn bg-primary text-white" href="#">
                                                    Add Project
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                            
                            <?php
                            if($projects){
                              
                            ?>
                            <div class="grid lg:grid-cols-3 gap-6 mb-6 ">

                              <?php 
                              foreach ($projects as $key => $project) {
                                $client_name = ($project['client_name']) ? $project['client_name'] : '---';
                                $selectedProj = '';
                                $selectedClass = '';
                                if($project['Project_Id'] == $seleted_project_id){
                                  $selectedProj = 'selected';
                                  $selectedClass = 'text-white';
                                }
                              ?>
                                <div class="<?php echo $selectedProj; ?> card">
                                    <div class="p-5">
                                        <div class="flex justify-between items-center">
                                            <h4 class="card-title <?php echo $selectedClass; ?> mb-4"><?php echo $project['Name']; ?></h4>
                                        </div>
                                        <div class="flex items-center mb-3">
                                            <h4 class="text-muted text-base">Client : </h4>
                                            <h4 class="<?php echo $selectedClass; ?> text-base px-2"><?php echo $client_name; ?></h4>
                                        </div>
                                        <div class="flex text-center">
                                            <div class="w-1/2">
                                                <h4 class="text-xl <?php echo $selectedClass; ?>"><?php echo $project['duration']; ?></h4>
                                                <p class="text-muted">Days</p>
                                            </div>
                                            <div class="w-1/2">
                                                <h4 class="text-xl <?php echo $selectedClass; ?>"><?php echo $project['project_value']; ?></h4>
                                                <p class="text-muted">Project Value</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php 
                                } 
                                ?>

                            </div>
                            <?php 
                              } else{
                            ?>
                            <div style="text-align: center;">
                              No Projects Found!
                            </div>
                            <?php 
                              } 
                            ?>




                        </div>
                        <!-- New section added for list View -->
                        <!-- <div class="project-list hidden">
                            <div class="card-header flex justify-end items-center mb-6">
                                <a href="javascript:void(0);" onclick="onProjectListBackClick()" class="btn btn-sm bg-light !text-sm text-gray-800 mr-2">Cards</a>
                                <a href="javascript:void(0);" data-fc-type="modal" data-target="#projectNew" class="btn btn-sm bg-light !text-sm text-gray-800 ">Add</a>
                            </div>
                            <div class="py-3">
                                <div class="overflow-x-auto">
                                    <div class="min-w-full inline-block align-middle">
                                        <div class="border rounded-lg overflow-hidden">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <tbody class="divide-y divide-gray-200">
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">1</td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                                            <p class="text-muted">Project Name</p>
                                                            <h4 class="text-xl text-black">Cost Test</h4>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                                            <p class="text-muted">Start Date</p>
                                                            <h4 class="text-xl text-black">01-08-2023</h4>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                                            <p class="text-muted">End Date</p>
                                                            <h4 class="text-xl text-black">01-08-2023</h4>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                                            <p class="text-muted">Project Duration</p>
                                                            <h4 class="text-xl text-black">459 Days</h4>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                                            <p class="text-muted">Project Value</p>
                                                            <h4 class="text-xl text-black">1000000</h4>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                                            <p class="text-muted">Client Name</p>
                                                            <h4 class="text-xl text-black">Test</h4>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                                            <p class="text-muted">Client Name</p>
                                                            <h4 class="text-xl text-black">Test</h4>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                                            <button  type="button" class="nav-link p-2 waves-effect">
                                                                <span class="flex items-center justify-center h-6 w-6">
                                                                    <i class="ph-fill ph-star text-2xl"></i>
                                                                </span>
                                                            </button>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                                            <a class="btn bg-primary text-white hover:text-gray-400" href="#"><span class="ph ph-trash text-base font-medium pe-1"></span>Deactivate Project</a>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                                            <button  type="button" class="nav-link p-2 waves-effect">
                                                                <span class="flex items-center justify-center h-6 w-6">
                                                                    <i class="ph ph-pencil-simple text-2xl"></i>
                                                                </span>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">2</td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                                            <p class="text-muted">Project Name</p>
                                                            <h4 class="text-xl text-black">Cost Test</h4>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                                            <p class="text-muted">Start Date</p>
                                                            <h4 class="text-xl text-black">01-08-2023</h4>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                                            <p class="text-muted">End Date</p>
                                                            <h4 class="text-xl text-black">01-08-2023</h4>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                                            <p class="text-muted">Project Duration</p>
                                                            <h4 class="text-xl text-black">459 Days</h4>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                                            <p class="text-muted">Project Value</p>
                                                            <h4 class="text-xl text-black">1000000</h4>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                                            <p class="text-muted">Client Name</p>
                                                            <h4 class="text-xl text-black">Test</h4>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                                            <p class="text-muted">Client Name</p>
                                                            <h4 class="text-xl text-black">Test</h4>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                                            <button  type="button" class="nav-link p-2 waves-effect">
                                                                <span class="flex items-center justify-center h-6 w-6">
                                                                    <i class="ph ph-star text-2xl"></i>
                                                                </span>
                                                            </button>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                                            <a class="btn bg-primary text-white hover:text-gray-400" href="#"><span class="ph ph-trash text-base font-medium pe-1"></span>Deactivate Project</a>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                                            <button  type="button" class="nav-link p-2 waves-effect">
                                                                <span class="flex items-center justify-center h-6 w-6">
                                                                    <i class="ph ph-pencil-simple text-2xl"></i>
                                                                </span>
                                                            </button>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">3</td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                                            <p class="text-muted">Project Name</p>
                                                            <h4 class="text-xl text-black">Cost Test</h4>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                                            <p class="text-muted">Start Date</p>
                                                            <h4 class="text-xl text-black">01-08-2023</h4>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                                            <p class="text-muted">End Date</p>
                                                            <h4 class="text-xl text-black">01-08-2023</h4>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                                            <p class="text-muted">Project Duration</p>
                                                            <h4 class="text-xl text-black">459 Days</h4>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                                            <p class="text-muted">Project Value</p>
                                                            <h4 class="text-xl text-black">1000000</h4>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                                            <p class="text-muted">Client Name</p>
                                                            <h4 class="text-xl text-black">Test</h4>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                                            <p class="text-muted">Client Name</p>
                                                            <h4 class="text-xl text-black">Test</h4>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                                            <button  type="button" class="nav-link p-2 waves-effect">
                                                                <span class="flex items-center justify-center h-6 w-6">
                                                                    <i class="ph ph-star text-2xl"></i>
                                                                </span>
                                                            </button>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                                            <a class="btn bg-primary text-white hover:text-gray-400" href="#"><span class="ph ph-trash text-base font-medium pe-1"></span>Deactivate Project</a>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                                            <button  type="button" class="nav-link p-2 waves-effect">
                                                                <span class="flex items-center justify-center h-6 w-6">
                                                                    <i class="ph ph-pencil-simple text-2xl"></i>
                                                                </span>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    
                                                </tbody>
                                            </table>
                                        </div>   
                                    </div>                                           
                                </div>
                            </div>
                        </div> -->
                    </div>

                    <!-- Tab 2 : Projects Schedule -->
                    <!-- <div id="card-type-tab-2" class="hidden" role="tabpanel" aria-labelledby="card-type-tab-item-2">
                        <div class="project-schedule">
                            <div class="flex justify-between items-center mb-6">
                                <h4 class="text-slate-900 text-lg font-medium">Project - Cost Project</h4>
            
                                <div class="md:flex hidden items-center gap-3 text-sm font-semibold">
                                    <a href="#" class="text-sm font-medium text-slate-700">Project</a>
                                    <i class="bx bx-chevron-right text-lg flex-shrink-0 text-slate-400"></i>
                                    <a href="#" class="text-sm font-medium text-slate-700">Schedule</a>
                                </div>
                            </div>
                            <div class="py-3">
                                <div class="overflow-x-auto">
                                    <div class="min-w-full inline-block align-middle">
                                        <div class="border rounded-lg overflow-hidden">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Sl.No</th>
                                                        <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Schedule Item</th>
                                                        <th scope="col" class="px-6 py-3 text-end text-sm text-gray-500">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200">
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">1</td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">Front-end Developer </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                                            <a class="btn bg-primary text-white hover:text-gray-400" href="#"  onclick="onProjectScheduleClick(1)"><span class="ph ph-person-simple-run text-base font-medium pe-1"></span>Activities</a>
                                                        </td>
                                                    </tr>
    
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">2</td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">Designer</td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                                            <a class="btn bg-primary text-white hover:text-gray-400" href="#" onclick="onProjectScheduleClick(2)"><span class="ph ph-person-simple-run text-base font-medium pe-1"></span>Activities</a>
                                                        </td>
                                                    </tr>
    
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">3</td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">Director of Product</td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                                            <a class="btn bg-primary text-white hover:text-gray-400" href="#" onclick="onProjectScheduleClick(3)"><span class="ph ph-person-simple-run text-base font-medium pe-1"></span>Activities</a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>   
                                    </div>                                           
                                </div>
                            </div>
                        </div>

                        <div class="schedule-activity-content hidden">
                            <div class="flex justify-between items-center mb-6">
                                <h4 class="text-slate-900 text-lg font-medium">Activities</h4>
            
                                <div class="md:flex hidden items-center gap-3 text-sm font-semibold">
                                    <a href="#" class="text-sm font-medium text-slate-700">Project</a>
                                    <i class="bx bx-chevron-right text-lg flex-shrink-0 text-slate-400"></i>
                                    <a href="#" onclick="goToProjectScheduleList()" class="text-sm font-medium text-slate-700">Schedule</a>
                                    <i class="bx bx-chevron-right text-lg flex-shrink-0 text-slate-400"></i>
                                    <a href="#" class="text-sm font-medium text-slate-700">Activities</a>
                                </div>
                            </div>

                            <div class="flex justify-end items-center">
                                <button class="btn btn-sm bg-light !text-sm text-gray-800 mb-6" type="button" onclick="goToProjectScheduleList()">Back</button>
                            </div>
                            <div class="py-3">
                                <div class="overflow-x-auto">
                                    <div class="min-w-full inline-block align-middle">
                                        <div class="border rounded-lg overflow-hidden">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Sl.No</th>
                                                        <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Activity Name</th>
                                                        <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Unit</th>
                                                        <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Quantity</th>
                                                        <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Duration</th>
                                                        <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Start Date</th>
                                                        <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">End Date</th>
                                                        <th scope="col" class="px-6 py-3 text-end text-sm text-gray-500">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200">
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">1</td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">React Development </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">Mtr </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">1500 </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">58 </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">01-06-2023 </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">01-06-2023 </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                                            <a class="btn bg-primary text-white hover:text-gray-400" href="#"  onclick="onProjectScheduleTaskClick(1)"><span class="ph ph-check-square text-base font-medium pe-1"></span>Tasks</a>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">2</td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">HTML/ CSS development</td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">Mtr </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">1500 </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">58 </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">01-06-2023 </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">01-06-2023 </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                                            <a class="btn bg-primary text-white hover:text-gray-400" href="#" onclick="onProjectScheduleTaskClick(2)"><span class="ph ph-check-square text-base font-medium pe-1"></span>Tasks</a>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">3</td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">JavaScript along with form validations</td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">Mtr </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">1500 </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">58 </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">01-06-2023 </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">01-06-2023 </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                                            <a class="btn bg-primary text-white hover:text-gray-400" href="#" onclick="onProjectScheduleTaskClick(3)"><span class="ph ph-check-square text-base font-medium pe-1"></span>Tasks</a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="schedule-activity-task-content hidden">
                            <div class="flex justify-between items-center mb-6">
                                <h4 class="text-slate-900 text-lg font-medium">Tasks</h4>
            
                                <div class="md:flex hidden items-center gap-3 text-sm font-semibold">
                                    <a href="#" class="text-sm font-medium text-slate-700">Project</a>
                                    <i class="bx bx-chevron-right text-lg flex-shrink-0 text-slate-400"></i>
                                    <a href="#" onclick="goToProjectScheduleList()" class="text-sm font-medium text-slate-700">Schedule</a>
                                    <i class="bx bx-chevron-right text-lg flex-shrink-0 text-slate-400"></i>
                                    <a href="#" onclick="cancelActivitiesTasks()" class="text-sm font-medium text-slate-700">Activities</a>
                                    <i class="bx bx-chevron-right text-lg flex-shrink-0 text-slate-400"></i>
                                    <a href="#" class="text-sm font-medium text-slate-700">Task</a>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-6 mb-3">
                                <div>
                                    <h6 class="text-sm mb-2">Report Type : </h6>
                                    <div class="flex gap-2">
                                        <div class="form-check">
                                            <input type="radio" class="form-radio text-primary" name="formRadio" id="formRadio01" checked>
                                            <label class="ms-1.5" for="formRadio01">Fixed Quantity </label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" class="form-radio text-primary" name="formRadio" id="formRadio02">
                                            <label class="ms-1.5" for="formRadio02">Variable Quantity</label>
                                        </div>
                                    </div>
                                </div> 
                                <div>
                                    <p class="text-gray-700"><span class="font-medium">Estimated Quantity : </span>1500 Mtr</p>
                                </div>                           
                            </div>
                            
                            <div class="py-3">
                                <div class="overflow-x-auto">
                                    <div class="min-w-full inline-block align-middle">
                                        <div class="border rounded-lg overflow-hidden">
                                            <table class="min-w-full divide-y divide-gray-200 bg-white">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Task</th>
                                                        <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Duration(Hours)</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200">
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                            <input type="text" id="simpleinput" class="form-input" placeholder="Boring">
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                            <input type="text" id="simpleinput" class="form-input" placeholder="15">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                            <input type="text" id="simpleinput" class="form-input" placeholder="Boring">
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                            <input type="text" id="simpleinput" class="form-input" placeholder="15">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                            <input type="text" id="simpleinput" class="form-input" placeholder="Boring">
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                            <input type="text" id="simpleinput" class="form-input" placeholder="15">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                            <input type="text" id="simpleinput" class="form-input" placeholder="Boring">
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                            <input type="text" id="simpleinput" class="form-input" placeholder="15">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-end text-gray-800">Cycle Time:</td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">20</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>   
                                    </div>                                           
                                </div>
                            </div>

                            <div class="py-3">
                                <div class="overflow-x-auto">
                                    <div class="min-w-full inline-block align-middle">
                                        <div class="border rounded-lg overflow-hidden">
                                            <table class="min-w-full divide-y divide-gray-200 bg-white">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th scope="col" class="px-2 py-3 text-start text-sm text-gray-500">Cycle Quantity (Mtr)</th>
                                                        <th scope="col" class="px-2 py-3 text-start text-sm text-gray-500">No. of Cycles</th>
                                                        <th scope="col" class="px-2 py-3 text-start text-sm text-gray-500">Working Hours</th>
                                                        <th scope="col" class="px-2 py-3 text-start text-sm text-gray-500">No. of Resource units</th>
                                                        <th scope="col" class="px-2 py-3 text-start text-sm text-gray-500">No. of Units per Cycle</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200">
                                                    <tr>
                                                        <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">
                                                            <input type="text" id="simpleinput" class="form-input" placeholder="Boring">
                                                        </td>
                                                        <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">
                                                            <input type="text" id="simpleinput" class="form-input" placeholder="15">
                                                        </td>
                                                        <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">
                                                            <input type="text" id="simpleinput" class="form-input" placeholder="Boring">
                                                        </td>
                                                        <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">
                                                            <input type="text" id="simpleinput" class="form-input" placeholder="15">
                                                        </td>
                                                        <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">
                                                            <div class="flex gap-2">
                                                                <input type="text" id="simpleinput" class="form-input" placeholder="Boring">
                                                                <input type="text" id="simpleinput" class="form-input" placeholder="Boring">
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>   
                                    </div>                                           
                                </div>
                            </div>
                            <div class="card-header flex items-center my-3">
                                <a href="javascript:void(0);" class="btn btn-sm bg-light !text-sm text-gray-800 mr-2" onclick="cancelActivitiesTasks()">Save</a>
                                <a href="javascript:void(0);" class="btn btn-sm bg-light !text-sm text-gray-800 mr-2" onclick="cancelActivitiesTasks()">Cancel</a>
                            </div>
                        </div>
                    </div> -->


                    <!-- Tab 3 : Reporting -->
                    <div id="card-type-tab-3" class="hidden" role="tabpanel" aria-labelledby="card-type-tab-item-3">
                        <div class="reporting">
                            <div class="flex justify-between items-center mb-6">
                                <h4 class="text-slate-900 text-lg font-medium">Progress Report</h4>
            
                                <div class="md:flex  items-center gap-3 text-sm font-semibold">
                                    <!-- <a href="#" class="text-sm font-medium text-slate-700">Project</a>
                                    <i class="bx bx-chevron-right text-lg flex-shrink-0 text-slate-400"></i>
                                    <a href="#" class="text-sm font-medium text-slate-700">Reporting</a> -->
                                    <a href="javascript:void(0);" class="btn btn-sm bg-light !text-sm text-gray-800 mr-2" onclick="onHistoryClick()">History</a>

                                </div>
                            </div>
                            <!-- <div class="card-header flex justify-end items-center mb-6">
                                <a href="javascript:void(0);" class="btn btn-sm bg-light !text-sm text-gray-800 mr-2" onclick="onHistoryClick()">History</a>
                            </div> -->
                            <div class="grid lg:grid-cols-2 gap-6 mb-6 " id="activity-list">
                                <div class="text-center" style="padding:20%">
                                    Loading...
                                </div>
                            </div>
                        </div>
                        
                        <!-- New section added for History -->
                        <div class="history-content hidden">
                            <div class="flex justify-between items-center mb-6">
                                <h4 class="text-slate-900 text-lg font-medium">History</h4>
            
                                <div class="md:flex hidden items-center gap-3 text-sm font-semibold">
                                    <a href="#" class="text-sm font-medium text-slate-700">Project</a>
                                    <i class="bx bx-chevron-right text-lg flex-shrink-0 text-slate-400"></i>
                                    <a href="#"  onclick="goToReportingList()" class="text-sm font-medium text-slate-700">Reporting</a>
                                    <i class="bx bx-chevron-right text-lg flex-shrink-0 text-slate-400"></i>
                                    <a href="#" class="text-sm font-medium text-slate-700">History</a>
                                </div>
                            </div>
                            <!-- Back button with onlick attached -->
                            <div class="flex justify-end items-center">
                                <button class="btn btn-sm bg-light !text-sm text-gray-800" type="button" onclick="goToReportingList()">Back</button>
                            </div>
                            <!-- History Table -->
                            <div class="py-3">
                                <div class="overflow-x-auto">
                                    <div class="min-w-full inline-block align-middle">
                                        <div class="border rounded-lg overflow-hidden">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Activity Name</th>
                                                        <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Total Quantity</th>
                                                        <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Completed Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200">
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">1</td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">Front-end Developer </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">02-06-2023 </td>
                                                    </tr>
    
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">2</td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">Front-end Developer </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">02-06-2023 </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- New section added for Report Resources -->
                        <div class="report-resources hidden">
                            <div class="flex justify-between items-center mb-6">
                                <h4 class="text-slate-900 text-lg font-medium">Report Resources</h4>

                                <div class="md:flex hidden items-center gap-3 text-sm font-semibold">
                                    <a href="#" class="text-sm font-medium text-slate-700">Project</a>
                                    <i class="bx bx-chevron-right text-lg flex-shrink-0 text-slate-400"></i>
                                    <a href="#"  onclick="onReportResourcesCancel()" class="text-sm font-medium text-slate-700">Reporting</a>
                                    <i class="bx bx-chevron-right text-lg flex-shrink-0 text-slate-400"></i>
                                    <a href="#" class="text-sm font-medium text-slate-700">Report Resources</a>
                                </div>
                            </div>
                            <!-- Back button with onlick attached -->
                            <div class="flex justify-end items-center">
                                <button class="btn btn-sm bg-light !text-sm text-gray-800" type="button" onclick="onReportResourcesCancel()">Back</button>
                            </div>
                            <!-- Report Resuorce  Table -->

                             <div class="py-3">
                                <div class="flex justify-between items-center mb-6">
                                    <p class="text-gray-700"><span class="font-medium">Activity : </span>Construction of 1200 Dia Piles- 30M Deoth</p>

                                    <p class="text-gray-700"><span class="font-medium">Work Hours : </span>24</p>
                                </div>
                                <!--Report Resources Material -->
                                <div class="report-materials">
                                    <nav class="flex space-x-2">
                                        <button type="button" class="bg-primary text-white py-3 px-4 inline-flex items-center gap-2 text-sm font-medium text-center rounded-lg hover:text-gray-400" >
                                            <span class="ph ph-wall text-base font-medium pe-1"></span><span class="md:flex hidden">Materials</span>
                                        </button>
                                        <button type="button"  onclick="onMaterialsLabourClick()" class="bg-gray-200 text-gray-600 py-3 px-4 inline-flex items-center gap-2 text-sm font-medium text-center rounded-lg hover:text-gray-500">
                                            <span class="ph ph-person text-base font-medium pe-1"></span><span class="md:flex hidden">Direct Labour</span>
                                        </button>
                                        <button type="button" onclick="onMaterialsEquipmentlick()" class="bg-gray-200 text-gray-600 py-3 px-4 inline-flex items-center gap-2 text-sm font-medium text-center rounded-lg hover:text-gray-500">
                                            <span class="ph ph-hammer text-base font-medium pe-1"></span><span class="md:flex hidden">Plant Equipment</span>
                                        </button>
                                        <button type="button" onclick="onMaterialsSubContractorClick()" class="bg-gray-200 text-gray-600 py-3 px-4 inline-flex items-center gap-2 text-sm font-medium text-center rounded-lg hover:text-gray-500">
                                            <span class="ph ph-user-gear text-base font-medium pe-1"></span><span class="md:flex hidden">Sub Contractor</span>
                                        </button>
                                    </nav>
                                    <!-- Material Tab Form-->
                                    <div class="my-6">
                                        <div class="grid lg:grid-cols-2 gap-6">

                                            <div class="mb-3">
                                                <div class="flex">
                                                    <div class="flex items-center justify-center border border-[#e0e6ed] bg-[#eee] px-3 font-semibold ltr:rounded-l-md ltr:border-r-0 rtl:rounded-r-md">
                                                        IssuedDate
                                                    </div>
                                                    <input class="form-input" id="example-date" type="date" name="date">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <div class="flex items-center">
                                                    <input type="checkbox" class="form-checkbox rounded text-primary" id="customCheck1">
                                                    <label class="ms-1.5" for="customCheck1">Keep quantity as Zero</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="py-3">
                                        <div class="overflow-x-auto">
                                            <div class="min-w-full inline-block align-middle">
                                                <div class="border rounded-lg overflow-hidden">
                                                    <table class="min-w-full divide-y divide-gray-200 bg-white">
                                                        <thead class="bg-light">
                                                            <tr>
                                                                <th scope="col" class="px-2 py-3 text-start text-sm text-gray-500">Resource Type</th>
                                                                <th scope="col" class="px-2 py-3 text-start text-sm text-gray-500">Resource</th>
                                                                <th scope="col" class="px-2 py-3 text-start text-sm text-gray-500">Unit</th>
                                                                <th scope="col" class="px-2 py-3 text-start text-sm text-gray-500">Quantity</th>
                                                                <th scope="col" class="px-2 py-3 text-start text-sm text-gray-500">Cum Quantity</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-200">
                                                            <tr>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">Materials</td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">25mm CRS bars</td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">Ton</td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">
                                                                    <input type="text" id="example-disable"  class="form-input">
                                                                </td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">1500</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">Materials</td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">25mm CRS bars</td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">Ton</td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">
                                                                    <input type="text" id="example-disable"  class="form-input">
                                                                </td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">1500</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">Materials</td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">25mm CRS bars</td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">Ton</td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">
                                                                    <input type="text" id="example-disable"  class="form-input">
                                                                </td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">1500</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>   
                                            </div>                                           
                                        </div>
                                    </div>
                                    <div class="card-header flex items-center my-3">
                                        <a href="javascript:void(0);" class="btn btn-sm bg-light !text-sm text-gray-800 mr-2" onclick="cancelActivitiesTasks()">Save</a>
                                        <a href="javascript:void(0);" class="btn btn-sm bg-light !text-sm text-gray-800 mr-2" onclick="cancelActivitiesTasks()">Cancel</a>
                                    </div>
                                </div>

                                <!--Report Resources Direct Labour -->
                                <div class="report-labour hidden">
                                    <nav class="flex space-x-2">
                                        <button type="button" onclick="onLabourMaterialClick()" class="bg-gray-200 text-gray-600 py-3 px-4 inline-flex items-center gap-2 text-sm font-medium text-center rounded-lg hover:text-gray-400" >
                                            <span class="ph ph-wall text-base font-medium pe-1"></span><span class="md:flex hidden">Materials</span>
                                        </button>
                                        <button type="button" class="bg-primary text-white py-3 px-4 inline-flex items-center gap-2 text-sm font-medium text-center rounded-lg hover:text-gray-500">
                                            <span class="ph ph-person text-base font-medium pe-1"></span><span class="md:flex hidden">Direct Labour</span>
                                        </button>
                                        <button type="button" onclick="onLabourEquipmentlick()" class="bg-gray-200 text-gray-600 py-3 px-4 inline-flex items-center gap-2 text-sm font-medium text-center rounded-lg hover:text-gray-500">
                                            <span class="ph ph-hammer text-base font-medium pe-1"></span><span class="md:flex hidden">Plant Equipment</span>
                                        </button>
                                        <button type="button" onclick="onLabourSubContractorClick()" class="bg-gray-200 text-gray-600 py-3 px-4 inline-flex items-center gap-2 text-sm font-medium text-center rounded-lg hover:text-gray-500">
                                            <span class="ph ph-user-gear text-base font-medium pe-1"></span><span class="md:flex hidden">Sub Contractor</span>
                                        </button>
                                    </nav>
                                    <!-- Material Tab Form-->
                                    <div class="my-6">
                                        <div class="grid lg:grid-cols-2 gap-6">

                                            <div class="mb-3">
                                                <div class="flex">
                                                    <div class="flex items-center justify-center border border-[#e0e6ed] bg-[#eee] px-3 font-semibold ltr:rounded-l-md ltr:border-r-0 rtl:rounded-r-md">
                                                        IssuedDate
                                                    </div>
                                                    <input class="form-input" id="example-date" type="date" name="date">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <div class="flex items-center">
                                                    <input type="checkbox" class="form-checkbox rounded text-primary" id="customCheck1">
                                                    <label class="ms-1.5" for="customCheck1">Keep quantity as Zero</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="py-3">
                                        <div class="overflow-x-auto">
                                            <div class="min-w-full inline-block align-middle">
                                                <div class="border rounded-lg overflow-hidden">
                                                    <table class="min-w-full divide-y divide-gray-200 bg-white">
                                                        <thead class="bg-light">
                                                            <tr>
                                                                <th scope="col" class="px-2 py-3 text-start text-sm text-gray-500">Resource Type</th>
                                                                <th scope="col" class="px-2 py-3 text-start text-sm text-gray-500">Resource</th>
                                                                <th scope="col" class="px-2 py-3 text-start text-sm text-gray-500">Unit</th>
                                                                <th scope="col" class="px-2 py-3 text-start text-sm text-gray-500">Quantity</th>
                                                                <th scope="col" class="px-2 py-3 text-start text-sm text-gray-500">Cum Quantity</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-200">
                                                            <tr>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">Materials</td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">25mm CRS bars</td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">Ton</td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">
                                                                    <input type="text" id="example-disable"  class="form-input">
                                                                </td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">1500</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">Materials</td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">25mm CRS bars</td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">Ton</td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">
                                                                    <input type="text" id="example-disable"  class="form-input">
                                                                </td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">1500</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">Materials</td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">25mm CRS bars</td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">Ton</td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">
                                                                    <input type="text" id="example-disable"  class="form-input">
                                                                </td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">1500</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>   
                                            </div>                                           
                                        </div>
                                    </div>
                                    <div class="card-header flex items-center my-3">
                                        <a href="javascript:void(0);" class="btn btn-sm bg-light !text-sm text-gray-800 mr-2" onclick="cancelActivitiesTasks()">Save</a>
                                        <a href="javascript:void(0);" class="btn btn-sm bg-light !text-sm text-gray-800 mr-2" onclick="cancelActivitiesTasks()">Cancel</a>
                                    </div>
                                </div>

                                <!--Report Resources Plan Equipment -->
                                <div class="report-equipment hidden">
                                    <nav class="flex space-x-2">
                                        <button type="button" onclick="onEquipmentMaterialClick()" class="bg-gray-200 text-gray-600 py-3 px-4 inline-flex items-center gap-2 text-sm font-medium text-center rounded-lg hover:text-gray-400" >
                                            <span class="ph ph-wall text-base font-medium pe-1"></span><span class="md:flex hidden">Materials</span>
                                        </button>
                                        <button type="button" onclick="onEquipmentLabourlick()" class="bg-gray-200 text-gray-600 py-3 px-4 inline-flex items-center gap-2 text-sm font-medium text-center rounded-lg hover:text-gray-500">
                                            <span class="ph ph-person text-base font-medium pe-1"></span><span class="md:flex hidden">Direct Labour</span>
                                        </button>
                                        <button type="button" class="bg-primary text-white py-3 px-4 inline-flex items-center gap-2 text-sm font-medium text-center rounded-lg hover:text-gray-500">
                                            <span class="ph ph-hammer text-base font-medium pe-1"></span><span class="md:flex hidden">Plant Equipment</span>
                                        </button>
                                        <button type="button" onclick="onEquipmentSubContractorClick()" class="bg-gray-200 text-gray-600 py-3 px-4 inline-flex items-center gap-2 text-sm font-medium text-center rounded-lg hover:text-gray-500">
                                            <span class="ph ph-user-gear text-base font-medium pe-1"></span><span class="md:flex hidden">Sub Contractor</span>
                                        </button>
                                    </nav>
                                    <!-- Material Tab Form-->
                                    <div class="my-6">
                                        <div class="grid lg:grid-cols-2 gap-6">

                                            <div class="mb-3">
                                                <div class="flex">
                                                    <div class="flex items-center justify-center border border-[#e0e6ed] bg-[#eee] px-3 font-semibold ltr:rounded-l-md ltr:border-r-0 rtl:rounded-r-md">
                                                        IssuedDate
                                                    </div>
                                                    <input class="form-input" id="example-date" type="date" name="date">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <div class="flex items-center">
                                                    <input type="checkbox" class="form-checkbox rounded text-primary" id="customCheck1">
                                                    <label class="ms-1.5" for="customCheck1">Keep quantity as Zero</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="py-3">
                                        <div class="overflow-x-auto">
                                            <div class="min-w-full inline-block align-middle">
                                                <div class="border rounded-lg overflow-hidden">
                                                    <table class="min-w-full divide-y divide-gray-200 bg-white">
                                                        <thead class="bg-light">
                                                            <tr>
                                                                <th scope="col" class="px-2 py-3 text-start text-sm text-gray-500">Resource Type</th>
                                                                <th scope="col" class="px-2 py-3 text-start text-sm text-gray-500">Resource</th>
                                                                <th scope="col" class="px-2 py-3 text-start text-sm text-gray-500">Quantity</th>
                                                                <th scope="col" class="px-2 py-3 text-start text-sm text-gray-500">Cum Quantity</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-200">
                                                            <tr>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">Materials</td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">
                                                                    <input type="text" id="example-disable"  class="form-input">
                                                                </td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">1500</td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">
                                                                    <input type="text" id="example-disable"  class="form-input">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">Materials</td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">
                                                                    <input type="text" id="example-disable"  class="form-input">
                                                                </td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">1500</td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">
                                                                    <input type="text" id="example-disable"  class="form-input">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">Materials</td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">
                                                                    <input type="text" id="example-disable"  class="form-input">
                                                                </td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">1500</td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">
                                                                    <input type="text" id="example-disable"  class="form-input">
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>   
                                            </div>                                           
                                        </div>
                                    </div>
                                    <div class="card-header flex items-center my-3">
                                        <a href="javascript:void(0);" class="btn btn-sm bg-light !text-sm text-gray-800 mr-2" onclick="cancelActivitiesTasks()">Save</a>
                                        <a href="javascript:void(0);" class="btn btn-sm bg-light !text-sm text-gray-800 mr-2" onclick="cancelActivitiesTasks()">Cancel</a>
                                    </div>
                                </div>

                                <!--Report Resources Sub Contractor -->
                                <div class="report-subcontractor hidden">
                                    <nav class="flex space-x-2">
                                        <button type="button" onclick="onSubContractorMaterialClick()" class="bg-gray-200 text-gray-600 py-3 px-4 inline-flex items-center gap-2 text-sm font-medium text-center rounded-lg hover:text-gray-400" >
                                            <span class="ph ph-wall text-base font-medium pe-1"></span><span class="md:flex hidden">Materials</span>
                                        </button>
                                        <button type="button" onclick="onSubContractorLabourlick()" class="bg-gray-200 text-gray-600 py-3 px-4 inline-flex items-center gap-2 text-sm font-medium text-center rounded-lg hover:text-gray-500">
                                            <span class="ph ph-person text-base font-medium pe-1"></span><span class="md:flex hidden">Direct Labour</span>
                                        </button>
                                        <button type="button" onclick="onSubContractorEquipmentClick()" class="bg-gray-200 text-gray-600 py-3 px-4 inline-flex items-center gap-2 text-sm font-medium text-center rounded-lg hover:text-gray-500">
                                            <span class="ph ph-hammer text-base font-medium pe-1"></span><span class="md:flex hidden">Plant Equipment</span>
                                        </button>
                                        <button type="button" class="bg-primary text-white py-3 px-4 inline-flex items-center gap-2 text-sm font-medium text-center rounded-lg hover:text-gray-500">
                                            <span class="ph ph-user-gear text-base font-medium pe-1"></span><span class="md:flex hidden">Sub Contractor</span>
                                        </button>
                                    </nav>
                                    <!-- Material Tab Form-->
                                    <div class="my-6">
                                        <div class="grid lg:grid-cols-2 gap-6">

                                            <div class="mb-3">
                                                <div class="flex">
                                                    <div class="flex items-center justify-center border border-[#e0e6ed] bg-[#eee] px-3 font-semibold ltr:rounded-l-md ltr:border-r-0 rtl:rounded-r-md">
                                                        IssuedDate
                                                    </div>
                                                    <input class="form-input" id="example-date" type="date" name="date">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <div class="flex items-center">
                                                    <input type="checkbox" class="form-checkbox rounded text-primary" id="customCheck1">
                                                    <label class="ms-1.5" for="customCheck1">Keep quantity as Zero</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="py-3">
                                        <div class="overflow-x-auto">
                                            <div class="min-w-full inline-block align-middle">
                                                <div class="border rounded-lg overflow-hidden">
                                                    <table class="min-w-full divide-y divide-gray-200 bg-white">
                                                        <thead class="bg-light">
                                                            <tr>
                                                                <th scope="col" class="px-2 py-3 text-start text-sm text-gray-500">Resource Type</th>
                                                                <th scope="col" class="px-2 py-3 text-start text-sm text-gray-500">Resource</th>
                                                                <th scope="col" class="px-2 py-3 text-start text-sm text-gray-500">Unit</th>
                                                                <th scope="col" class="px-2 py-3 text-start text-sm text-gray-500">Quantity</th>
                                                                <th scope="col" class="px-2 py-3 text-start text-sm text-gray-500">Cum Quantity</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-200">
                                                            <tr>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">Materials</td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">25mm CRS bars</td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">Ton</td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">
                                                                    <input type="text" id="example-disable"  class="form-input">
                                                                </td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">1500</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">Materials</td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">25mm CRS bars</td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">Ton</td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">
                                                                    <input type="text" id="example-disable"  class="form-input">
                                                                </td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">1500</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">Materials</td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">25mm CRS bars</td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">Ton</td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">
                                                                    <input type="text" id="example-disable"  class="form-input">
                                                                </td>
                                                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-800">1500</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>   
                                            </div>                                           
                                        </div>
                                    </div>
                                    <div class="card-header flex items-center my-3">
                                        <a href="javascript:void(0);" class="btn btn-sm bg-light !text-sm text-gray-800 mr-2" onclick="cancelActivitiesTasks()">Save</a>
                                        <a href="javascript:void(0);" class="btn btn-sm bg-light !text-sm text-gray-800 mr-2" onclick="cancelActivitiesTasks()">Cancel</a>
                                    </div>
                                </div>

                            </div> 
                            
                        </div>

                        <!-- New section added for Report Progress -->
                        <div class="report-progress hidden">
                            <div class="text-center" style="padding:20%">
                                Loading...
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    

</div>
<!-- Tab End -->

</main>