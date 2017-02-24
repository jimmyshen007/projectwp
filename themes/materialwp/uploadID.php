<?php
/**
 * Created by PhpStorm.
 * User: yangtianfang
 * Date: 1/02/2017
 * Time: 10:27 PM
 */
?>
<html>
<body>
<div class="panel panel-default">
    <div class="panel-heading"><h4>ID Verification</h4></div>
    <div class="panel-body">
        <p>Please upload your passport so we can verify your identification.</p>
        <form action="" id="upload-form" method="post">
            <table style="margin-bottom: 3px">
                <tbody>
                <tr>
                    <td style="width: 27%"></td>
                    <td style="width: 73%">
                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;"></div>
                            <div>
                                <span class="btn btn-raised btn-default btn-file" style="width: 200px;border-radius: 3px;border: 1px solid #009688;"><span class="fileinput-new" style="color: #009688">Upload Passport</span><span class="fileinput-exists" style="color: #009688">Change</span><input type="file" name="passport" id="passport"></span>
                                <a href="#" class="btn btn-raised btn-default fileinput-exists" data-dismiss="fileinput" style="border-radius: 3px;border: 1px solid #ff5722;"><span style="color: #ff5722">Remove</span></a>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 25%"></td>
                    <td style="width: 75%">
                        <label class="control-label" for="pp_expiry_date">Passport Expiry Date</label>
                        <input class="form-control input-lg"  style="width: 200px;" type="text" placeholder="yyyy-mm-dd" name="pp_expiry_date" id="pp_expiry_date"/>
                        <input type="hidden" name="action" value="uploadID">
                    </td>
                </tr>
                </tbody>
            </table>
            <div>
                <p id="errorTxt" class="text-danger" style="visibility: hidden">&nbsp;</p>
            </div>
            <table style="margin-top: -10px">
                <tbody>
                <tr>
                    <td width="33%"></td>
                    <td width="37%" ><input id="BtnSubmit" type="submit" style="width: 150px;height: 40px; border-radius: 3px;" class="btn btn-primary" name="submit" value="submit"/></td>
                    <td width="30%"></td>
                </tr>
                </tbody>
            </table>
        </form>
        <hr>
        <table style="margin-top: 5px">
            <tbody>
            <tr>
                <td width="70%">
                    <p style="margin-top: 18px">You can also provide your student ID optionally in</p>
                </td>
                <td width="30%"><a href="/register/" class="btn btn-default" style="border-radius: 3px;border: 1px solid #009688;"><span style="color: #009688">Profile</span></a></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>

