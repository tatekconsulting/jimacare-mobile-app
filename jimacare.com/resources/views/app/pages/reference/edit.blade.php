@extends('app.template.layout')

@section('content')
    <div class="about-content">
        <div class="container">
            <div class="row mt-5 pt-4 mb-5 pb-4">

                <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="row">
                        <div class="col-12">
                            <h3>Reference for {{$user->firstname}}  {{$user->lastname}}</h3>
                            <p>You're submitting a reference for {{$user->firstname}}  {{$user->lastname}}. This will help them complete their registration with
                                JimaCare</p>


                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-12">

                    <form action="{{route('reference.update')}}" method="post">
						@csrf
                        <h4>About you</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="first_name">First Name</label>
                                <input type="hidden" name="user_id" value="{{$user->id}}" >
                                <input type="hidden" name="type"  value="{{$reference}}">

                                <input type="text" name="first_name" class="form-control" id="first_name"
                                    placeholder="First Name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name">Last Name</label>
                                <input type="text" name="last_name" class="form-control" id="last_name"
                                    placeholder="Last Name">
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label for="email">Email Address</label>
                                <input type="email" name="email" class="form-control" id="email"
                                    placeholder="Email Address" required>
                            </div>
                            <div class="col-md-4">
                                <label for="job_title">Job Title</label>
                                <input type="text" name="job_title" class="form-control" id="job_title"
                                    placeholder="Job Title">
                            </div>
                            <div class="col-md-4">
                                <label for="organisation">Organisation</label>
                                <input type="text" name="organisation" class="form-control" id="organisation"
                                    placeholder="Organisation">
                            </div>
                        </div>

                        <h4 class="mt-4">User employment</h4>

                        <div class="row mt-2">

                            <div class="col-md-4">
                                <label for="from">From</label>
                                <input type="date" name="from" class="form-control" id="from" placeholder="From">
                            </div>
                            <div class="col-md-4">
                                <label for="to">To</label>
                                <input type="date" name="to" class="form-control" id="to" placeholder="To">
                            </div>
                            <div class="col-md-4">
                                <label for="emp_job_title">Employee Job Title</label>
                                <input type="text" name="emp_job_title" class="form-control" id="emp_job_title"
                                    placeholder="Employee Job Title">
                            </div>

                            <div class="col-md-6 mt-2">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="emp_currently_work"
                                        id="emp_currently_work">
                                    <label class="form-check-label" for="emp_currently_work">Is the employee currently
                                        working here?</label>
                                </div>
                            </div>

                        </div>


                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label for="emp_key_duty">Employee key duties</label>
                                <textarea type="text" name="emp_key_duty" class="form-control" id="emp_key_duty" placeholder="Employee key duties"> </textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="responsibility">What was their responsibility?</label>
                                <textarea type="text" name="responsibility" class="form-control" id="responsibility" placeholder="please write their responsibilities" required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="comment">Comment</label>
                                <textarea type="text" name="comment" class="form-control" id="comment" placeholder="Comment"></textarea>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        Were there any issues regarding safety (previous disciplinary issues/dismissals) or
                                        competencies during their employment?
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check">
                                            <input type="radio" class="form-check-input" name="emp_safety_issue"
                                                id="issue-yes" value="true">
                                            <label class="form-check-label" for="issue-yes">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" class="form-check-input" name="emp_safety_issue"
                                                id="issue-no" value="false">
                                            <label class="form-check-label" for="issue-no">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mt-2">
                                <div class="card">
                                    <div class="card-header">
                                        Would you employ user again?
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check">
                                            <input type="radio" class="form-check-input" name="emp_again"
                                                id="employ-yes" value="true">
                                            <label class="form-check-label" for="employ-yes">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" class="form-check-input" name="emp_again"
                                                id="employ-no" value="false">
                                            <label class="form-check-label" for="employ-no">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12 flex justify-end">
                                <input type="submit" value="Submit" class="btn btn-primary">
                            </div>
                        </div>
                    </form>
                </div>


            </div>
        </div>

    </div>
@endsection
