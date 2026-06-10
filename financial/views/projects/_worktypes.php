
<div class="panel panel-default project-type-masters-tab tab-wrapper tab acco-one">
    <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/masters/estimateworktype.js" type="text/javascript"></script>
    <!--<input type="radio" id="rd5" class="prjcttype-tab" name="rd">-->

    <div class="panel-heading">
      <h4 class="panel-title prjcttype-tab">
        <a data-toggle="collapse" data-parent="#accordionpromasterind" href="#collapseprotype">
        <span class="icon-presentation"></span>Project Type</a>
      </h4>
    </div>

    <div id="collapseprotype" class="tab-content cOrder-body panel-collapse collapse">
        <div class="panel-body "> 

            <div class="search-and-content-wrpr">
                        <div class="search-and-actions-wrpr row">
                            <div class="content-search-wrpr col-md-5 col-sm-5">
                                <input type="text" placeholder="Search" id="searchestworktypename" class="form-control" >
                                <button id="estworktypesearch" class="btn btn-primary" type="button"><span class="icon-search5"></span></button>
                            </div>
                            <div class="col-md-5 col-sm-5"></div>
                            <div class="content-action-wrpr col-md-2 col-sm-2">
                                <a href="#" id="addestworktype" class="btn btn-primary addForm" title="Add Project Type"><span class="icon-add"></span> Add</a>
                                <a href="#" class="btn btn-primary list-accountType" id="listestworktype"><span class="icon-th-list"></span> List</a>
                            </div>
                        </div>
                        <div class="content-wrpr">
                            <!-- form starts here -->
                            <div style="display: none;" class="add-form add-project-type-form">
                                <div class="row">
                                    <form id="estworktypeform">
                                    <div class="col-md-2"></div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Project Type</label>
                                            <input type="text" class="form-control" id="estworktypename" placeholder="Project Type">
                                            <span class="error" style="display: none;"></span>
                                        </div>  
                                    </div>
                                    <div class="col-md-4 text-left">
                                        <label></label>
                                        <button type="button" class="btn btn-danger cancel" id="canceladdtype"><span class="icon-close"></span> Cancel</button>
                                        <button type="button" class="btn btn-primary save-btn" id="saveestworktype"><span class="icon-check"></span> Add Project Type</button>
                                    </div>
                                                                        </form>
                                </div>
                            </div>
                            <!-- form ends here -->
                            
                            
                            
                            <!-- edit form starts here -->
                            <div  style="display: none;"  class="edit-form edit-project-type-form">
                                <div class="row">
                                    <div class="col-md-2"></div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Project Type</label>
                                            <input type="text" class="form-control" id="editestworktype" placeholder="Project Type">
                                            <span class="error" style="display: none;"></span>
                                        </div>  
                                    </div>
                                    <div class="col-md-4 text-left">
                                        <label></label>
                                        <button type="button" class="btn btn-danger cancel" id="canceladdtype"><span class="icon-close"></span> Cancel</button>
                                        <button type="button" class="btn btn-primary saveestworktypebutton" id=""><span class="icon-check"></span> Edit Project Type</button>
                                    </div>
                                    
                                </div>
                            </div> 
                            <!-- edit form ends here -->
                            
                            
                            
                            
                            
                            <!-- list start here -->
                            <div class="preloader" style="display: none;"><center>
                                <img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"></center>
                            </div>
                        <div id="estworktypeitems" class="project-type-cntnt-wrpr data-content-list">
                            
                               
                                
                                
                                <div class="row">
                                    <div class="col-md-1 text-center">
                                        <span class="number">2</span>
                                    </div>
                                    <div class="col-md-8 type">
                                        <label>Project Type</label>
                                        <span>Marine Works</span>
                                    </div>
                                    <div class="col-md-3 icon-groups type">
                                        
                                        <a href="#" class="btn btn-primary editForm icon-pencil"></a>
                                        <a href="#" class="btn btn-danger icon-trash1"></a>
                                    </div>
                                </div>
                                
                            </div>
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            <!-- list end here -->
                        
                            
                        </div>
                    </div>

<!--                     
            <div class="row show-grid"> 
                <div class="col-md-3"><button type="button" class="btn btn-success"  id="addestworktype"><span class="glyphicon glyphicon-plus-sign"></span>Add Worktype</button></div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listestworktype"><span class="glyphicon glyphicon-list-alt"></span>List Worktype</button></div>
            </div> -->
<!--             <div id="estworktypelistsection">
                <div id="searchdivestworktype" class="row show-grid" style="display: block;">
                    <div class="col-md-9">
                        <input type="text" placeholder="Search" id="searchestworktypename" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button id="estworktypesearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                 
                    <table class="table table-bordered" id="estworktypetable" style="display: table; overflow: hidden;">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Worktype</th>
                            <th colspan="2"></th>
                        </tr>
                        <tr class="preloader" style="display: none;"><td colspan="4" align="center"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                        </thead>
                        <tbody id="estworktypeitems" class="ui-sortable">

                        </tbody>
                    </table>
                </div>
            </div>
 -->            <!-- <div id="estworktypeaddsection" class="row show-grid">
                <form id="estworktypeform">
                    <table class="table table-bordered" style="overflow: hidden; display: table; background-color: rgb(226, 226, 226);" id="estworktypeadd">
                        <tbody><tr>
                            <th>#</th>
                            <th><input class="form-control" type="text" id="estworktypename" name="worktypename" placeholder="Worktype"><span class="error" style="display: none;"></span></th>
                            <th><button type="button" class="btn btn-danger" id="saveestworktype"><span class="glyphicon glyphicon-saved"></span>Save</button></th>
                        </tr>
                        </tbody></table>
                </form>
            </div> -->
        </div>
    </div>
</div>

