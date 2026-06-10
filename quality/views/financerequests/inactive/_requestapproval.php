<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/requestappr.js" type="text/javascript"></script>
<style>

    tbody#requestappritems .form-control {
        font-size: 14px;
    }
</style>

<div class="panel panel-default acco-one fund-approval-tab tab">
    <input type="radio" id="rd5" name="rd">
    <div class="panel-heading" >
        <h4 class="panel-title acc_trigger" id="requestappr">
        <a  href="javascript:void (0)">
        <span class="icon-dollar1 acc_trigger"></span>Fund Approval</a>
        </h4>
    </div>
    <div class="preloader" style="display: none;" align="center">
        <img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
    </div>
    <div id="funddetails"></div>                  
<!-- 
    <div class="account-heads-table-wrpr row">
                    <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="30">#</th>
                                <th>Purpose &amp; Amount </th>
                                <th>Date </th>
                                <th width="100">Mode </th>
                                <th>Account Head </th>
                                <th width="50">TDS </th>
                                <th width="50">SGST </th>
                                <th width="50">CGST </th>
                                <th width="50">IGST </th>
                                <th>Net Amount </th>
                                <th>Status </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center"><span class="number">1</span></td>
                                <td><textarea class="form-control">Bein SIB OD  interest for the month of May 2020</textarea><input  class="purpose-amount form-control" type="text" value="455129" /></td>
                                <td>Sep 05 2020 </td>
                                <td>
                                    <select class="form-control">
                                        <option>Bank</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control">
                                        <option>OD-Interest SIB </option>
                                    </select>
                                </td>
                                <td>
                                    <input class="form-control" value="0" />
                                </td>
                                <td>
                                    <input class="form-control" value="0" />
                                </td>
                                <td>
                                    <input class="form-control" value="0" />
                                </td>
                                <td>
                                    <input class="form-control" value="0" />
                                </td>
                                <td>455112.00</td>
                                <td>
                                    <div class="icon-groups">
                                        <a class="btn btn-primary innactive icon-check" href="#"></a>
                                        <a class="btn btn-primary icon-close" href="#"></a>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center"><span class="number">2</span></td>
                                <td><textarea class="form-control">Bein SIB OD  interest for the month of May 2020</textarea><input  class="purpose-amount form-control" type="text" value="455129" /></td>
                                <td>Sep 05 2020 </td>
                                <td>
                                    <select class="form-control">
                                        <option>Bank</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control">
                                        <option>OD-Interest SIB </option>
                                    </select>
                                </td>
                                <td>
                                    <input class="form-control" value="0" />
                                </td>
                                <td>
                                    <input class="form-control" value="0" />
                                </td>
                                <td>
                                    <input class="form-control" value="0" />
                                </td>
                                <td>
                                    <input class="form-control" value="0" />
                                </td>
                                <td>455112.00</td>
                                <td>
                                    <div class="icon-groups">
                                        <a class="btn btn-primary icon-check" href="#"></a>
                                        <a class="btn btn-primary innactive icon-close" href="#"></a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                    <div class="col-md-12 text-center">
                        <div class="form-groups">
                            <button class="btn btn-primary "><span class="icon-file3"></span>Save as Draft</button>
                            <button class="btn btn-primary "><span class="icon-check"></span>Approve</button>
                        </div>
                    </div>
                </div> -->
                
                     <!-- <form id="fundreqapprform"> -->

                     <div class="preloader" style="display: none;" align="center">
                            <img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                        </div> 


                    
                                <div class="account-heads-cards-wrpr">
                                    <div class="row">
                                        
                                        
                              <div id="funddetails"></div>

                              </div></div>
     


                  <div class="account-heads-table-wrpr row">
                                    <div class="col-md-12">

                                         <div id="fundapprtable"></div>
                                  
                                    </div>
                                     <div id="fundapprbtns" style="display: none">
                                    <div class="col-md-12 text-center">
                                        <div class="form-groups">
                                            <button class="btn btn-primary "  id="saveasdraftrequest" name="saveasdraftrequest"><span class="icon-file3"></span>Save as Draft</button>
                                            <button class="btn btn-primary " id="approveadvrequest" name="approveadvrequest"><span class="icon-check"></span>Approve</button>
                                        </div>
                                    </div></div>

                            </div>
                           <!--  </form>  -->
                                
                            </div>



              </div></div></div></div>














