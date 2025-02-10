@extends('layouts.admin')
@section('content')
    <div class="row mb-3">
        <div class="col-md-8">
            <!-- Notifications-->

            @if(Session::has('message'))
                <div class="alert alert-success alert-dismissible">
                    <ul>
                        <li>{{Session::get('message')}}</li>
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

        </div>

        <div class="col-md-4 text-right">
            <!-- Date and Time-->
            <p id="todayDate" class="small"><b></b></p>
            <h5 id="todayTime" class=""><b></b></h5>
        </div>
        <hr/>
    </div>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small" style="background-color:#f9f9f9; padding: .55rem 1rem;">
            <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('ib/loan/request') }}">Loan requests</a></li>
            <li class="breadcrumb-item active">Loan request details</li>
        </ol>
    </nav>


    <h5>Loan request details</h5>
    <hr/>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 mb-4">
                    <button type="button" class="btn btn-sm btn-outline-dark" onclick="printDiv('loanRequestDoc')">Print</button>
                </div>
                <hr/>
            </div>
            <div>
                <div class="row">
                    <div class="col-md-6">
                        <h6>User details</h6>
                        <table class="table table-sm">
                                <tbody>
                                    <tr><td style="width:200px">Name:</td><td>{{$request->users->name}}</td></tr>
                                    <tr><td>Email:</td><td>{{$request->users->email}}</td></tr>
                                    <tr><td>Mobile Number:</td><td>{{$request->users->mobile_phone ?? '-nil-'}}</td></tr>

                                    <tr><td>Account ID:</td><td>{{$request->users->accountID ?? '-nil-'}}</td></tr>
                                    <tr><td>Verified:</td>
                                        <td>
                                            @if($request->users->isVerified == 1)
                                                <span class="badge badge-warning">Not verified</span>
                                            @else
                                                <span class="badge badge-success">Verified</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <h6>Loan request details</h6>
                        <table class="table table-sm">
                            <tbody>
                                <tr><td style="width:200px">Borrower Name/Instituion:</td><td>
                                        @if($request->institute_id==null)
                                            {{$request->users->name}}
                                        @else
                                            {{$request->instutions->institute_name}}
                                        @endif</td></tr>
                                <tr><td>National ID:</td><td>{{$request->nin ?? '-nil-'}}</td></tr>
                                <tr><td>Request Date:</td><td>{{$request->loan_date}}</td></tr>
                                <tr><td>Loan Type:</td><td>{{$request->type->name ?? '-nil-'}}</td></tr>
                                <tr><td>Branch Name:</td><td>{{$request->branch->branchName ?? '-nil-'}}</td></tr>
                                <tr><td>Purpose:</td><td>{{$request->purpose_id ?? '-nil-'}}</td></tr>
                                <tr><td>Loan Security:</td><td>{{$request->laon_security_id ?? '-nil-'}}</td></tr>
                                <tr><td>Loan Status:</td>
                                    <td>
                                        @if($request->status_id == 1)
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($request->status_id == 2)
                                            <span class="badge badge-warning">On Progress</span>
                                        @elseif($request->status_id == 4)
                                            <span class="badge badge-success">Success</span>
                                        @elseif($request->status_id == 5)
                                            <span class="badge badge-danger">Rejected</span>
                                        @else
                                            <span class="badge badge-danger">Failed</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr><td>National ID:</td><td>{{$request->nin ?? '-nil-'}}</td></tr>

                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <div class="d-none" id="loanRequestDoc">
                <hr/>

                <div class="page" id="page0" style="width: 97%; height:1540px; ">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <center>
                                <img src="http://41.188.154.221:8003/images/logo.png" width="100%" height="auto" alt="">
                            </center>
                        </div>
                        <div class="col-md-3"></div>
                        <div class="col-md-6">
                            <h1 class="display-2 font-weight-bold"><i>MKOMBOZI</i></h1>
                            <h3>COMMERRICAL BANK PLC</h3>
                        </div>
                    </div>
                    <h4 class="font-weight-bold text-center pt-3">Loan Application Form for Salaried Individual</h4>
                    <hr/>

                    <div class="row mt-3 mb-3">
                        <div class="col-md-7">
                            <p class="mt-2">Branch Name: ______________________________________________</p>
                            <p>Date of Application: ________________________________________</p>
                            <p>Loan Application Number: __________________________________</p>
                            <p>Savings Account Number: __________________________________</p>

                        </div>
                        <div class="col-md-2"></div>

                        <div class="col-md-2">
                            <div class="card m-auto">
                                <div class="card-body">
                                    <p class="text-center"><i>Affix recent passport sice photograph of an applicant.</i></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1"></div>
                    </div>

                    <h5 class="mb-1">PART A: APPLICATION</h5>

                    <table class="table table-bordered  table-sm">
                        <tbody>

                            <tr><td class="font-weight-bold text-center"  style="padding-top:20px; padding-bottom:20px;">REQUEST FOR LOAN</td></tr>
                            <tr>
                                <td>
                                    <p>
                                        I _________________________________________ do hereby apply for a loan of the sum of Tsh. _________________________________________
                                        and comfirm that i will be bound by terms and conditions that administer the provision of credit facility to employed individuals.
                                        I understand that this loan will be paid by deducting installments from my monthly salary, which will be facilitated by my Employer.
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>


                    <table class="table table-bordered  table-sm">
                        <tbody>
                            <tr><td colspan="5" class="font-weight-bold text-center"  style="padding-top:20px; padding-bottom:20px;">Employee Details</td></tr>
                            <tr>
                                <td colspan="2" style="width: 20%">Date of Birth:</td>
                                <td colspan="3" style="width: 20%">Age:</td>
                            </tr>

                            <tr>
                                <td colspan="2" style="width: 20%">Gender</td>
                                <td style="width: 20%">Female: [&nbsp; &nbsp;]</td>
                                <td colspan="2" style="width: 20%">Male: [&nbsp; &nbsp;]</td>
                            </tr>

                            <tr>
                                <td style="width: 20%">Marital Status</td>
                                <td style="width: 20%">Single: [&nbsp; &nbsp;]</td>
                                <td style="width: 20%">Married: [&nbsp; &nbsp;]</td>
                                <td style="width: 20%">Divorced: [&nbsp; &nbsp;]</td>
                                <td style="width: 20%">Widow/er: [&nbsp; &nbsp;]</td>
                            </tr>

                            <tr>
                                <td colspan="1" style="width: 20%">Postal Address</td>
                                <td colspan="4" style="width: 20%"></td>
                            </tr>

                            <tr>
                                <td colspan="1" style="width: 20%">Physical Address</td>
                                <td colspan="4" style="width: 20%"></td>
                            </tr>

                            <tr>
                                <td colspan="1" style="width: 20%">Telephone</td>
                                <td colspan="4" style="width: 20%"></td>
                            </tr>

                            <tr>
                                <td colspan="1" style="width: 20%">Job Title</td>
                                <td colspan="4" style="width: 20%"></td>
                            </tr>

                            <tr>
                                <td colspan="2" style="width: 20%">Date of Employment:</td>
                                <td colspan="3" style="width: 20%">Nature of Tenure:</td>
                            </tr>

                            <tr>
                                <td colspan="2" style="width: 20%">Years of Service:</td>
                                <td colspan="3" style="width: 20%">Previous Employer:</td>
                            </tr>

                            <tr>
                                <td colspan="2" style="width: 20%">ID Card No.:</td>
                                <td colspan="3" style="width: 20%">Date Issue:</td>
                            </tr>

                            <tr>
                                <td colspan="5" style="width: 20%">Computer No./ Check No./ Payroll No.:</td>
                            </tr>

                            <tr>
                                <td colspan="2" style="width: 20%">Spouse/ Next of Kin Name:</td>
                                <td colspan="3" style="width: 20%">Telephone:</td>
                            </tr>

                        </tbody>
                    </table>
                    <table class="table table-bordered  table-sm">
                        <tbody>
                            <tr><td  style="padding-top:20px; padding-bottom:20px; width:16.6%" colspan="6" class="text-center">Applicant`s Other Financial Liability</td></tr>

                            <tr><td colspan="6" class="text-center">Do you jave any loans from another institution or from employer? (Yes) or (No)</td></tr>
                            <tr class="text-center">
                                <td>Bank/ Institution/ Employer</td>
                                <td>Account No.</td>
                                <td>Date Issued</td>
                                <td>Loan Amount</td>
                                <td>Current Balance</td>
                                <td>Maturity Date</td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>

                            <tr>
                                <td>&nbsp;</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="page" id="page1" style="width: 97%; height:1540px; ">
                    <table class="table table-bordered table-sm">
                        <tbody>
                            <tr>
                                <td colspan="2" class="font-weight-bold text-center"  style="padding-top:20px; padding-bottom:20px;">Qualifying Amount</td>
                            </tr>
                            <tr><td style="width:250px">Net salary per month</td><td></td></tr>
                            <tr><td>Other month financial benefits</td><td></td></tr>
                            <tr><td>Total Net Salary</td><td></td></tr>
                            <tr><td>Total Net Salary x 12</td><td></td></tr>
                            <tr><td>The interest rate applicable</td><td></td></tr>
                            <tr><td>Loan period</td><td></td></tr>
                            <tr><td colspan="2"  style="padding-top:10px; padding-bottom:20px;"><br/><span style="padding-left:50px"></span>Applicant`s Signature: ___________________________________________<span style="padding-left:50px"></span>Date: ___________________________________________</td></tr>
                        </tbody>
                    </table>
                    <table class="table table-bordered  table-sm">
                        <tbody>

                            <tr><td colspan="2" class="font-weight-bold text-center"  style="padding-top:20px; padding-bottom:20px;">Employer Details</td></tr>
                            <tr><td style="width:150px">Employer`s Name</td><td></td></tr>
                            <tr><td>Nature of Business</td><td></td></tr>
                            <tr><td>Employer Address</td><td></td></tr>

                            <tr><td colspan="2" class="font-weight-bold text-center"  style="padding-top:20px; padding-bottom:20px;">Employer Decleration</td></tr>
                            <tr>
                                <td colspan="2">
                                    <p>We comfirm that the applicant whose photograph is attached is an employee of our company/institution.
                                        All the information she/he has provided are correct.
                                        We comfirm that in case the loan application is approved, my institution shall be bound to deduct and remit the agreed loan installment to Mkombozi Commercial Bank PLC.
                                        We comfirm and declare that we have been authorized to endorse this application on behalf of the organization.
                                        <br/>
                                        <div class="row mt-5">
                                            <div class="col-md-1"></div>
                                            <div class="col-md-5">
                                                <p class="mt-2">Authorized Signatory: __________________________________</p>
                                                <p>Title: _____________________________________________________</p>
                                                <p>Date: _____________________________________________________</p>

                                            </div>
                                            <div class="col-md-1"></div>

                                            <div class="col-md-4">
                                                <div class="card m-auto">
                                                    <div class="card-body">
                                                        <p class="text-center"><i>Affix official company<br>Stamp.</i></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-1"></div>
                                        </div>
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table table-bordered  table-sm">
                        <tbody>
                            <tr><td  style="padding-top:20px; padding-bottom:20px;" colspan="2" class="text-center"><h6>Loan Approval <i>(For Bank`s Official use only)</i></h6></td></tr>
                            <tr><td style="width:50%" colspan="2">Loan Applied:</td></tr>
                            <tr><td>Loan Applied:</td><td>Loan Term:</td></tr>
                            <tr><td>Employer Code:</td><td>Loan A/C:</td></tr>
                            <tr><td style="padding-top:10px; padding-bottom:10px;">Name of Loan Officer:</td><td style="padding-top:10px; padding-bottom:10px;">Signature:</td></tr>
                            <tr><td style="padding-top:10px; padding-bottom:10px;">Branch Manager:</td><td style="padding-top:10px; padding-bottom:10px;">Signature:</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="page" id="page2" style="width: 97%;  height:1540px; ">
                    <h4>Terms and condition of salaries loan</h4>
                    <ol>
                        <li>
                            <p>Interest charges shall be calculated on the outstanding balance under the loan, the rate specified in the letter of the offer.</p>
                        </li>
                        <li>
                            <p>The loan inclusive interest will be paid on due date as indicated on the payment schedule.</p>
                        </li>
                        <li>
                            <p>The bank reserves the right to change the bases to which the interest will be calculated or to vary interest rate as the bank will consider it being necessary.</p>
                        </li>
                        <li>
                            <p>
                                The following fees are payable by the borrower upon signing the offer letter by deducting from the loan amount
                                <ul>
                                    <li>Aggreement fee</li>
                                    <li>Insurance premium payable as condition for acceptance of the offer.</li>
                                </ul>
                            </p>
                        </li>
                        <li>
                            <p>The loan shall be paid on equal installment deducted from the borrower monthly salary.</p>
                        </li>
                        <li>
                            <p>The borrower`s salary shall be required to pass through the bank or employer to deduct installment from employees salary and submit to the bank on monthly basis.</p>
                        </li>
                        <li>
                            <p>Security forming collateral for the loan shall be all benefits and payment due or borrower entitled from employer or any person if the same through the employer.</p>
                        </li>
                        <li>
                            <p>The borrower agrees to take Credit Life assurance from insurer acceptable by the bank for loan purpose for securing outstanding balances against risks of death or permanent disability and so assigns the bank all rights arising out of the policy.</p>
                        </li>
                        <li>
                            <p>Early liquidation of the loan before maturity is allowed. On early liquidation, borrower will enjoy 50% discount on the outstanding interest and 100% for those liquidating for the purpose of obtaining another loan.</p>
                        </li>
                        <li>
                            <p>The bank may claim immediate payment of the outstanding amount interms of aggreements all of which shall then become due; if the borrower fails to company with the terms and condition of this loan.</p>
                        </li>
                    </ol>
                    <p class="mt-5">
                        I accept that i have read, understood and agree to be bound by the salaried loan terms and condition as written on the application form and the offer letter.
                    </p>
                    <br/>
                    <br/>
                    <hr/>
                    <br/>
                    <br/>
                    <p class="pl-5">
                        Name:___________________________________________________<br/><br/>
                       Signature:_______________________________________________<br/><br/>
                        Date:_____________________________________________________<br/><br/>
                    </p>
                </div>

            <!--
                <div class="row">
                    <div class="col-md-12 text-center mb-5">
                        <center>
                            <img src="https://via.placeholder.com/200" width="100px" height="auto" alt=""/>
                            <h4>MKOMBOZI Commercial Bank (Sample Doc heading)</h4>
                            <p>The address details goes here.</p>
                            <p class="small">If there are any other details that need to be shown on the header will go here.</p>
                            <hr/>
                        </center>
                    </div>
                    <div class="col-md-12 mb-5">
                        <h2>Client loan request.</h2>
                    </div>
                    <div class="col-md-6">
                        <h6>User details</h6>
                        <table class="table table-sm">
                                <tbody>
                                    <tr><td style="width:200px">Name:</td><td>{{$request->users->name}}</td></tr>
                                    <tr><td>Email:</td><td>{{$request->users->email}}</td></tr>
                                    <tr><td>Mobile Number:</td><td>{{$request->users->mobile_phone ?? '-nil-'}}</td></tr>

                                    <tr><td>Account ID:</td><td>{{$request->users->accountID ?? '-nil-'}}</td></tr>
                                    <tr><td>Verified:</td>
                                        <td>
                                            @if($request->users->isVerified == 1)
                                                <span class="badge badge-warning font-weght-bold">Not verified</span>
                                            @else
                                                <span class="badge badge-success font-weght-bold">Verified</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                        </table>
                    </div>

                    <div class="col-md-6 mb-5">
                        <h6>Loan request details</h6>
                        <table class="table table-sm">
                            <tbody>
                                <tr><td style="width:200px">Borrower Name:</td><td>{{$request->borrower_name}}</td></tr>
                                <tr><td>National ID:</td><td>{{$request->nin ?? '-nil-'}}</td></tr>
                                <tr><td>Request Date:</td><td>{{$request->loan_date}}</td></tr>
                                <tr><td>Loan Type:</td><td>{{$request->type->name ?? '-nil-'}}</td></tr>
                                <tr><td>Branch Name:</td><td>{{$request->branch->branchName ?? '-nil-'}}</td></tr>
                                <tr><td>Username:</td><td>{{$request->users->name ?? '-nil-'}}</td></tr>
                                <tr><td>Purpose:</td><td>{{$request->purpose_id ?? '-nil-'}}</td></tr>
                                <tr><td>Loan Security:</td><td>{{$request->laon_security_id ?? '-nil-'}}</td></tr>
                                <tr><td>Loan Status:</td>
                                    <td>
                                        @if($request->status_id == 1)
                                            <span class="badge badge-warning">Pending</span>
                                        @else
                                            <span class="badge badge-success">Accepted</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr><td>National ID:</td><td>{{$request->nin ?? '-nil-'}}</td></tr>

                            </tbody>
                        </table>
                    </div>

                    <div class="col-md-12">
                        <h6>Additional details:</h6>
                        <p>If there is any other details then it will be set here and it all depends on the Template to be provided.<br/>
                            For demonstration purposes this area is filled with a dummy text just to see how it will be.
                        </p>
                    </div>

                    <div class="col-md-12 text-center">
                        <div class="row">

                            <div class="col-md-3">
                                <center>
                                <p class="small mb-3">Signature & Date</p>
                                <p class="mt-6">________________________________________</p>
                                <p class="font-weight-bold">Somebody Name</p>
                                </center>
                            </div>
                            <div class="col-md-3">
                                <center>
                                <p class="small mb-3">Signature & Date</p>
                                <p class="mt-6">________________________________________</p>
                                <p class="font-weight-bold">Somebody Name</p>
                                </center>
                            </div>
                            <div class="col-md-3">
                                <center>
                                <p class="small mb-3">Signature & Date</p>
                                <p class="mt-6">________________________________________</p>
                                <p class="font-weight-bold">Somebody Name</p>
                                </center>
                            </div>


                        </div>
                    </div>

                    <div class="col-md-12 mt-5">
                        <hr/>
                        <p class="small">This document was printed by Somebody on Somedate</p>
                    </div>

                </div>

            -->
            </div>
        </div>
    </div>
@section('scripts')
    @parent
    <script>
        //add an event listener on the print btn
        function printDiv(divName) {
            var printContents = document.getElementById(divName).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
    </script>
@endsection

@endsection
