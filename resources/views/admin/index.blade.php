@php
  $bookings = App\Models\Booking::latest()->get();
  $pending = App\Models\Booking::where('status','0')->get();
  $complete = App\Models\Booking::where('status','1')->get();
  $totalPrice = App\Models\Booking::sum('total_price');
  $today = Carbon\Carbon::now()->toDateString();
  $todayprice = App\Models\Booking::whereDate('created_at',$today)->sum('total_price');

  $allData = App\Models\Booking::orderBy('id','desc')->limit(10)->get();
@endphp
@extends('admin.admin_dashboard')
@section('main')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<div class="page-content">
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
       <div class="col">
         <div class="card radius-10 border-start border-0 border-4 border-info">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Total Booking</p>
                        <h4 class="my-1 text-info">{{count($bookings)}}</h4>
                        <p class="mb-0 font-13">Today Sale:  ${{ $todayprice }}</p>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-gradient-blues text-white ms-auto"><i class='bx bxs-cart'></i>
                    </div>
                </div>
            </div>
         </div>
       </div>
       <div class="col">
        <div class="card radius-10 border-start border-0 border-4 border-danger">
           <div class="card-body">
               <div class="d-flex align-items-center">
                   <div>
                    <p class="mb-0 text-secondary">Pening Booking</p>
                    <h4 class="my-1 text-danger">{{ count($pending) }}</h4>
                       <p class="mb-0 font-13">+5.4% from last week</p>
                   </div>
                   <div class="widgets-icons-2 rounded-circle bg-gradient-burning text-white ms-auto"><i class='bx bxs-wallet'></i>
                   </div>
               </div>
           </div>
        </div>
      </div>
      <div class="col">
        <div class="card radius-10 border-start border-0 border-4 border-success">
           <div class="card-body">
               <div class="d-flex align-items-center">
                   <div>
                    <p class="mb-0 text-secondary">Complete Booking</p>
                    <h4 class="my-1 text-success">{{ count($complete) }}</h4>
                       <p class="mb-0 font-13">-4.5% from last week</p>
                   </div>
                   <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto"><i class='bx bxs-bar-chart-alt-2' ></i>
                   </div>
               </div>
           </div>
        </div>
      </div>
      <div class="col">
        <div class="card radius-10 border-start border-0 border-4 border-warning">
           <div class="card-body">
               <div class="d-flex align-items-center">
                   <div>
                    <p class="mb-0 text-secondary">Total Price</p>
                    <h4 class="my-1 text-warning">${{ $totalPrice  }}</h4>
                       <p class="mb-0 font-13">+8.4% from last week</p>
                   </div>
                   <div class="widgets-icons-2 rounded-circle bg-gradient-orange text-white ms-auto"><i class='bx bxs-group'></i>
                   </div>
               </div>
           </div>
        </div>
      </div> 
    </div><!--end row-->

    <div class="row">
       <div class="col-12 col-lg-12 d-flex">
          <div class="card radius-10 w-100">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <div>
                        <h6 class="mb-0">Sales Overview</h6>
                    </div>
                    
                </div>
            </div>
              
              <div class="row row-cols-1 row-cols-md-3 row-cols-xl-3 g-0 row-group text-center border-top">
                
                <canvas id="bookingChart"></canvas>
              </div>
          </div>
       </div>
       
    </div><!--end row-->

     <div class="card radius-10">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div>
                    <h6 class="mb-0">Recent Orders</h6>
                </div>
                <div class="dropdown ms-auto">
                    <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown"><i class='bx bx-dots-horizontal-rounded font-22 text-option'></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="javascript:;">Action</a>
                        </li>
                        <li><a class="dropdown-item" href="javascript:;">Another action</a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="javascript:;">Something else here</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>B No</th>
                            <th>B Date</th>
                            <th>Customer</th>
                            <th>Room</th>
                            <th>Check IN/Out</th>
                            <th>Total Room</th>
                            <th>Guest</th> 
                        </tr>
                    </thead>
                    <tbody>
                       @foreach ($allData as $key=> $item ) 
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td> <a href="{{ route('edit_booking',$item->id) }}"> {{ $item->code }} </a></td>
                            <td> {{ $item->created_at->format('d/m/Y') }} </td>
                            <td> {{ $item['user']['name'] }} </td>
                            <td> {{ $item['room']['type']['name'] }} </td>
                            <td> <span class="badge bg-primary">{{ $item->check_in }}</span>   <span class="badge bg-warning text-dark">{{ $item->check_out }}</span> </td>
                            <td> {{ $item->number_of_rooms }} </td>
                            <td> {{ $item->person }} </td>
  
  
                        </tr>
                        @endforeach 
  
                    </tbody>
  
                </table>
            </div>
        </div>
        </div>


        <div class="row">
            <div class="col-12 col-lg-7 col-xl-8 d-flex">
              <div class="card radius-10 w-100">
                <div class="card-header bg-transparent">
                    <div class="d-flex align-items-center">
                        <div>
                            <h6 class="mb-0">Recent Orders</h6>
                        </div>
                        <div class="dropdown ms-auto">
                            <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown"><i class='bx bx-dots-horizontal-rounded font-22 text-option'></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="javascript:;">Action</a>
                                </li>
                                <li><a class="dropdown-item" href="javascript:;">Another action</a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="javascript:;">Something else here</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                   </div>
                 <div class="card-body">
                    <div class="row">
                      <div class="col-lg-7 col-xl-8 border-end">
                         <div id="geographic-map-2"></div>
                      </div>
                      <div class="col-lg-5 col-xl-4">
                       
                        <div class="mb-4">
                        <p class="mb-2"><i class="flag-icon flag-icon-us me-1"></i> USA <span class="float-end">70%</span></p>
                        <div class="progress" style="height: 7px;">
                             <div class="progress-bar bg-primary progress-bar-striped" role="progressbar" style="width: 70%"></div>
                         </div>
                        </div>
   
                        <div class="mb-4">
                         <p class="mb-2"><i class="flag-icon flag-icon-ca me-1"></i> Canada <span class="float-end">65%</span></p>
                         <div class="progress" style="height: 7px;">
                             <div class="progress-bar bg-danger progress-bar-striped" role="progressbar" style="width: 65%"></div>
                         </div>
                        </div>
   
                        <div class="mb-4">
                         <p class="mb-2"><i class="flag-icon flag-icon-gb me-1"></i> England <span class="float-end">60%</span></p>
                         <div class="progress" style="height: 7px;">
                             <div class="progress-bar bg-success progress-bar-striped" role="progressbar" style="width: 60%"></div>
                           </div>
                        </div>
   
                        <div class="mb-4">
                         <p class="mb-2"><i class="flag-icon flag-icon-au me-1"></i> Australia <span class="float-end">55%</span></p>
                         <div class="progress" style="height: 7px;">
                             <div class="progress-bar bg-warning progress-bar-striped" role="progressbar" style="width: 55%"></div>
                           </div>
                        </div>
   
                        <div class="mb-4">
                         <p class="mb-2"><i class="flag-icon flag-icon-in me-1"></i> India <span class="float-end">50%</span></p>
                         <div class="progress" style="height: 7px;">
                             <div class="progress-bar bg-info progress-bar-striped" role="progressbar" style="width: 50%"></div>
                           </div>
                        </div>

                        <div class="mb-0">
                           <p class="mb-2"><i class="flag-icon flag-icon-cn me-1"></i> China <span class="float-end">45%</span></p>
                           <div class="progress" style="height: 7px;">
                               <div class="progress-bar bg-dark progress-bar-striped" role="progressbar" style="width: 45%"></div>
                             </div>
                        </div>

                      </div>
                    </div>
                 </div>
               </div>
            </div>
   
            <div class="col-12 col-lg-5 col-xl-4 d-flex">
                <div class="card w-100 radius-10">
                 <div class="card-body">
                  <div class="card radius-10 border shadow-none">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">Total Likes</p>
                                <h4 class="my-1">45.6M</h4>
                                <p class="mb-0 font-13">+6.2% from last week</p>
                            </div>
                            <div class="widgets-icons-2 bg-gradient-cosmic text-white ms-auto"><i class='bx bxs-heart-circle'></i>
                            </div>
                        </div>
                    </div>
                 </div>
                 <div class="card radius-10 border shadow-none">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">Comments</p>
                                <h4 class="my-1">25.6K</h4>
                                <p class="mb-0 font-13">+3.7% from last week</p>
                            </div>
                            <div class="widgets-icons-2 bg-gradient-ibiza text-white ms-auto"><i class='bx bxs-comment-detail'></i>
                            </div>
                        </div>
                    </div>
                 </div>
                 <div class="card radius-10 mb-0 border shadow-none">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">Total Shares</p>
                                <h4 class="my-1">85.4M</h4>
                                <p class="mb-0 font-13">+4.6% from last week</p>
                            </div>
                            <div class="widgets-icons-2 bg-gradient-kyoto text-dark ms-auto"><i class='bx bxs-share-alt'></i>
                            </div>
                        </div>
                    </div>
                  </div>
                 </div>

                </div>
   
            </div>
         </div><!--end row-->

         <div class="row row-cols-1 row-cols-lg-3">
             <div class="col d-flex">
               <div class="card radius-10 w-100">
                   <div class="card-body">
                    <p class="font-weight-bold mb-1 text-secondary">Weekly Revenue</p>
                    <div class="d-flex align-items-center mb-4">
                        <div>
                            <h4 class="mb-0">$89,540</h4>
                        </div>
                        <div class="">
                            <p class="mb-0 align-self-center font-weight-bold text-success ms-2">4.4% <i class="bx bxs-up-arrow-alt mr-2"></i>
                            </p>
                        </div>
                    </div>
                    <div class="chart-container-0 mt-5">
                        <canvas id="chart3"></canvas>
                      </div>
                   </div>
               </div>
             </div>
             <div class="col d-flex">
                <div class="card radius-10 w-100">
                    <div class="card-header bg-transparent">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-0">Orders Summary</h6>
                            </div>
                            <div class="dropdown ms-auto">
                                <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown"><i class='bx bx-dots-horizontal-rounded font-22 text-option'></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="javascript:;">Action</a>
                                    </li>
                                    <li><a class="dropdown-item" href="javascript:;">Another action</a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="javascript:;">Something else here</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container-1 mt-3">
                            <canvas id="chart4"></canvas>
                          </div>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex bg-transparent justify-content-between align-items-center border-top">Completed <span class="badge bg-gradient-quepal rounded-pill">25</span>
                        </li>
                        <li class="list-group-item d-flex bg-transparent justify-content-between align-items-center">Pending <span class="badge bg-gradient-ibiza rounded-pill">10</span>
                        </li>
                        <li class="list-group-item d-flex bg-transparent justify-content-between align-items-center">Process <span class="badge bg-gradient-deepblue rounded-pill">65</span>
                        </li>
                    </ul>
                </div>
              </div>
              <div class="col d-flex">
                <div class="card radius-10 w-100">
                     <div class="card-header bg-transparent">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-0">Top Selling Categories</h6>
                            </div>
                            <div class="dropdown ms-auto">
                                <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown"><i class='bx bx-dots-horizontal-rounded font-22 text-option'></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="javascript:;">Action</a>
                                    </li>
                                    <li><a class="dropdown-item" href="javascript:;">Another action</a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="javascript:;">Something else here</a>
                                    </li>
                                </ul>
                            </div>
                         </div>
                     </div>
                    <div class="card-body">
                       <div class="chart-container-0">
                         <canvas id="chart5"></canvas>
                       </div>
                    </div>
                    <div class="row row-group border-top g-0">
                        <div class="col">
                            <div class="p-3 text-center">
                                <h4 class="mb-0 text-danger">$45,216</h4>
                                <p class="mb-0">Clothing</p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="p-3 text-center">
                                <h4 class="mb-0 text-success">$68,154</h4>
                                <p class="mb-0">Electronic</p>
                            </div>
                         </div>
                    </div><!--end row-->
                </div>
              </div>
         </div><!--end row-->

</div>

<script>
    var ctx = document.getElementById('bookingChart').getContext('2d');
    var bookings = @json($bookings);
  
    // Extract the required data from the bookings
    var labels = bookings.map(function(booking) {
        return booking.check_in; 
    });
    var data = bookings.map(function(booking) {
        return booking.total_price;
    });
    var bookingChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Booking Data',
                data: data,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
  </script>
@endsection