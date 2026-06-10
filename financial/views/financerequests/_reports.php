<?php 

use app\models\Projects;

?>

<div class="panel panel-default reports-tab acco-ten tab">
    <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/finance/financereports.js" type="text/javascript"></script>
    <input type="radio" id="rd5" name="rd">                   
    <div class="panel-heading" >
      <h4 class="panel-title">
        <a  href="#">
        <span class="icon-note1"></span>Reports</a>
      </h4>
    </div>

    <div  class="tab-content panel-collapse cOrder-body ">
        <div class="panel-body">
            <div class="search-and-content-wrpr">
                <div class="content-wrpr">
                    <div class="report-list-wrpr">                               
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-3">
                            
                                <div class="card">
                                    <div class="card-body">
                                        <span>1. Project Expenditure</span>
                                        <span class="icon-groups"><a href="#" class="btn btn-primary"><span class="icon-eye" id="viewprojectexpd"></span></a></span>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-md-3">
                            
                                <div class="card">
                                    <div class="card-body">
                                        <span>2. Corporate Expenditure</span>
                                        <span class="icon-groups"><a href="#" class="btn btn-primary"><span class="icon-eye" id="viewcorporateexpd"></span></a></span>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-md-3"></div>
                            <div class="col-md-3"></div>
                            <div class="col-md-3">
                            
                                <div class="card">
                                    <div class="card-body">
                                        <span>3. Balance Sheet</span>
                                        <span class="icon-groups"><a href="#" class="btn btn-primary"><span class="icon-eye" id="viewbalancesheet"></span></a></span>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-md-3">
                            
                                <div class="card">
                                    <div class="card-body">
                                        <span>4. Profit and loss</span>
                                        <span class="icon-groups"><a href="#" class="btn btn-primary"><span class="icon-eye" id="viewprofitandloss"></span></a></span>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-md-3"></div>
                 
                        </div>
                                    
                    </div>
                </div>
            </div>
        </div>
        <div id="projectexp" style="display:none;">
            <div class="search-and-content-wrpr">
                <div class="search-and-actions-wrpr row">
                    <div class="content-search-wrpr col-md-12 col-sm-12">
                        <select class="form-control" id="projexpproject" name="project">
                            <option value="0">Select Project</option>
                            <?php
                                $project=Projects::find()->where(['Project_Delete_Status' => 0])->all();
                                foreach($project AS $list):
                                    echo "<option value='".$list->Project_Id."'>".$list->Name."</option>";
                                endforeach;
                            ?>
                        </select>
                        <input class="form-control" type="date" id="pefromdate" name="pefromdate" value="" /> 
                        <input class="form-control" type="date" id="petodate" name="petodate" value=""/>
                        <button id="pesearch" class="btn btn-primary" type="button"><span class="icon-search5"></span></button>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <span class='error' id="rep_place_error" style="float: left;font-size: 12px;color: red;"></span>
                        </div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4"></div>
                    </div>
                </div>
                <div class="content-wrpr">
                    
                    <div class="prj-exp-list-wrpr" id="peitems">

                        <div class="row list-head">
                            <div class="col-md-12 text-center type">
                                <h5>A2Z Engineers and Pile Foundation</h5>
                                From <span class="date"><em class="cal-icon icon-calendar1"></em>Sep-28-2020</span> to <span class="date"><em class="cal-icon icon-calendar1"></em>Sep-28-2020</span>
                            </div>
                        </div>
                        <div class="custom-print-btn">
                            <div class="icon-groups">
                                <a href="#" class="btn btn-primary text-button"><span class="icon-print"></span>Print</a>                                
                            </div>
                        </div>
                                
                        <div class="row">
                            <div class="table-wrpr">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Expense</th>
                                            <th>Amount</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    
                                        <tr>
                                            <td class="table-subhead" colspan="3"><strong>Investments</strong></td>
                                            
                                        </tr>
                                        <tr>
                                            <td>Contract Income</td>
                                            <td class="text-right"></td>
                                            <td class="text-right">435,150,549.92</td>
                                        </tr>
                                        <tr>
                                            <td>Interest Income</td>
                                            <td class="text-right"></td>
                                            <td  class="text-right">2,588,974.90</td>
                                        </tr>
                                        <tr>
                                            <td class="text-right">Total</td>
                                            <td class="text-right">0</td>
                                            <td class="text-right">2,588,974.90</td>
                                        </tr>
                                                                                
                                        <tr>
                                            <td class="table-subhead" colspan="3"><strong>Expense</strong></td>
                                            
                                        </tr>
                                        <tr>
                                            <td>Admin Charges - EPF</td>
                                            <td class="text-right">15,324.00</td>
                                            <td class="text-right"></td>
                                        </tr>
                                        <tr>
                                            <td>Boarding and Lodging Charges</td>
                                            <td class="text-right"></td>
                                            <td  class="text-right">21,024.00</td>
                                        </tr>
                                        <tr>
                                            <td class="text-right">Total</td>
                                            <td class="text-right">88,974.90</td>
                                            <td class="text-right">0</td>
                                        </tr>
                                        <tr>
                                            <td class="text-right"><strong>Total</strong></td>
                                            <td class="text-right"><strong>319,307.36</strong></td>
                                            <td class="text-right"><strong>380,033.56</strong></td>
                                        </tr>
     
                                    </tbody>
                                </table>
                            </div>
                        </div>                                              
                        
                    </div>
                    
                </div>
                    
            </div>      

        </div>
    </div>

    <div id="corprtexp"></div>

    <div id="balancesheet"></div>

    <div id="profitandloss"></div>

</div>