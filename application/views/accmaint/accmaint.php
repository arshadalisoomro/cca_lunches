<div class="container page">

    <?php $this->renderFeedbackMessages(); ?>

    <div class="pull-right"><button id="btnCreateNewAccount" type="button" class="btn btn-success btn-large">Create New Account</button></div>
	<h2>Account Maintenance</h2>
    <hr />
    <table id="tblAcctMaint" class="table table-condensed table-striped table-bordered table-header">
        <thead>
            <tr>
                <th>Account</th>
                <th>Email</th>
                <th>Attached Users</th>
            </tr>
        </thead>
        <tbody>
            <?php
                echo $this->tableBody;
            ?>
        </tbody>
    </table>

    <div class="modal" id="modalAccount" tabindex="-1" role="dialog" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div id="title">Edit Account</div>
                </div>
                <div class="modal-body">
                    <form role="form" id="formAccount" class="form-inline">

                        <div class="form-group">
                            <label for="aname_acc">Account Name</label>
                            <input type="text" class="form-control" id="aname_acc" name="aname_acc" placeholder="(last name, mother & father first names)"/>
                            <br /><br />
                        </div>


                        <div class="form-group">
                            <label for="uname">Login Name</label>
                            <input type="text" class="form-control" id="uname" name="uname" placeholder="(usually email address)"/>
                            <br /><br />
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email"/>
                            <br /><br />
                        </div>


                        <div class="form-group" id="grpacctype">
                            <label for="atype" style="width: 140px;">Account Type</label>
                            <select id="atype" name="atype">
                                <option value="1">Standard</option>
                                <option value="2">Admin</option>
                            </select>
                            <br /><br />
                        </div>


                        <div class="form-group" id="grpaccactive">
                            <label for="uactive" style="width: 140px;">Account Active</label>
                            <select id="uactive" name="uactive">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                            <br /><br />
                        </div>


                        <div class="form-group" id="grpaccallowneworders">
                            <label for="nnorders" style="width: 140px;">Allow New Orders</label>
                            <select id="nnorders" name="nnorders">
                                <option value="0">Yes</option>
                                <option value="1">No</option>
                            </select>
                        </div>

                        <input type="hidden" class="account_id" name="account_id"/>

                    </form>
                </div>
                <div class="modal-footer">
                    <button id="btnAccountDelete" type="button" class="btn btn-danger btn-sm hide">Delete</button>
                    <button id="btnAccountOK" type="button" class="btn btn-primary btn-sm">OK</button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="modalUser" tabindex="-1" role="dialog" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div id="title">Edit User</div>
                </div>
                <div class="modal-body">
                    <form role="form" id="formUser" class="form-inline">

                        <div class="form-group">
                            <label for="aname_user">Associated Account</label>
                            <input type="text" class="form-control" id="aname_user" name="aname_user" disabled/>
                            <br /><br />
                        </div>


                        <div class="form-group">
                            <label for="fname">User First Name</label>
                            <input type="text" class="form-control" id="fname" name="fname"/>
                            <br /><br />
                        </div>


                        <div class="form-group">
                            <label for="lname">User Last Name</label>
                            <input type="text" class="form-control" id="lname" name="lname"/>
                            <br /><br />
                        </div>


                        <div class="form-group">
                            <label for="utype" style="width: 80px;">User Type</label>
                            <select id="utype" name="utype">
                                <!--<option value="0">(unassigned)</option>-->
                                <option value="1">Student</option>
                                <option value="2">Teacher</option>
                                <option value="3">Staff</option>
                                <option value="4">Parent</option>
                            </select>
                            <br /><br />
                        </div>


                        <div id="grpuserteachers" class="form-group">
                            <label for="tid">Teacher</label>
                            <select id="tid" name="tid">
                                <?php
                                echo '<option selected value="1">[ Please Select ]</option>';
                                foreach ($this->teachers as $teacher) {
                                    echo '<option value="'.$teacher->id.'">'.$teacher->lastName.', '.$teacher->firstName.' ('.$teacher->gradeDesc.')</option>';
                                }
                                ?>
                            </select>
                            <br /><br />
                        </div>

                        <div class="form-group" id="grpuserallowedtoorder">
                            <label for="ato" style="width: 85px;float: left;margin-top:5px;">Allowed to Order</label>
                            <select id="ato" name="ato">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <input type="hidden" class="account_id" name="account_id"/>
                        <input type="hidden" class="user_id" name="user_id"/>
                    </form>
                </div>
                <div class="modal-footer">
                    <button id="btnUserDelete" type="button" class="btn btn-danger btn-sm hide">Delete</button>
                    <button id="btnUserOK" type="button" class="btn btn-primary btn-sm">OK</button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>