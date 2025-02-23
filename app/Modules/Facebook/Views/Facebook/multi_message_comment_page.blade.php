@php
   $showTopBar = false;
   $showLeftSidebar = false;
   $showRightSidebar = false;
   $showSidePanel = false;
   $showPopupWraper = false;
   $showFooter = false;
@endphp

@extends('user.layouts.master')

@section('title', 'Profile')

@section('head.scripts')
<link rel="stylesheet" href="{{ asset('InterfaceModules/DeviceEmulator/Android/Multi_message_comment/css/multi_message_commnet.css') }}">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="{{ asset('InterfaceModules/DeviceEmulator/Android/Multi_message_comment/js/multi_message_commnet.js') }}"></script>
@endsection

@section('content')
<section>
   <div class="gap2 no-gap gray-bg">
      <div class="container-fluid no-padding">
         <div class="row">
            <div class="col-lg-12">
               <div class="message-users">
                  <!-- header message -->
                  <div class="message-head">
                     <h4>Chat Messages</h4>
                     <div class="more">
                        <div class="more-post-optns"><i class="ti-settings"></i>
                           <ul>
                              <li><i class="fa fa-wrench"></i>Setting</li>
                              <li><i class="fa fa-envelope-open"></i>Active Contacts</li>
                              <li><i class="fa fa-folder-open"></i>Archives Chats</li>
                              <li><i class="fa fa-eye-slash"></i>Unread Chats</li>
                              <li><i class="fa fa-flag"></i>Report a problem</li>
                           </ul>
                        </div>
                     </div>
                  </div>
                  <!-- search -->
                  <div class="message-people-srch">
                     <form method="post">
                        <input type="text" placeholder="Search Friend..">
                        <button type="submit"><i class="fa fa-search"></i></button>
                     </form>
                     <div class="btn-group add-group" role="group">
                        <button id="btnGroupDrop2" type="button" class="btn group dropdown-toggle user-filter" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                           All
                        </button>
                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop2">
                           <a class="dropdown-item" href="#">Online</a>
                           <a class="dropdown-item" href="#">Away</a>
                           <a class="dropdown-item" href="#">unread</a>
                           <a class="dropdown-item" href="#">archive</a>
                        </div>
                     </div>
                     <div class="btn-group add-group align-right" role="group">
                        <button id="btnGroupDrop1" type="button" class="btn group dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                           Create+
                        </button>
                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                           <a class="dropdown-item" href="#">New user</a>
                           <a class="dropdown-item" href="#">New Group +</a>
                           <a class="dropdown-item" href="#">Private Chat +</a>
                        </div>
                     </div>
                  </div>
                  <!-- message -->
                  <div class="mesg-peple">
                     <ul class="nav nav-tabs nav-tabs--vertical msg-pepl-list">
                       
                
                        @foreach($data as $items)
                      
                        @foreach($items->messages->message as $message)

                           <li class="nav-item unread">
                              <a class="active" href="#link1" data-uid="{{$items->messages->me->uid }}">
                                 <figure>
                                    @if(isset($items->messages->me->profile_avatar) &&$items->messages->me->profile_avatar)
                                       <img src="{{$items->messages->me->profile_avatar }}" onerror="this.onerror=null;this.src='/user/images/resources/friend-avatar3.jpg';" alt="">
                                    @else
                                       <img src="/user/images/resources/friend-avatar3.jpg" alt="">
                                    @endif
                                    <span class="status f-online"></span>
                                 </figure>
                                 <div class="user-name">
                                    @if(isset($items->messages->me->name) && isset($message->name))
                                       <h6 class="">{{$items->messages->me->name }} - {{ $message->name }}</h6>
                                    @else
                                       <h6 class="">{{$items->messages->me->name ?? 'Unknown' }} - {{ $message->name ?? '' }}</h6>
                                    @endif
                                    @if(!empty($message->message))
                                       <span>{{ substr($message->message, 0, 35) }}</span>
                                    @endif
                                 </div>
                                 <div class="more">
                                    <div class="more-post-optns"><i class="ti-more-alt"></i>
                                       <ul>
                                          <li><i class="fa fa-bell-slash-o"></i>Mute</li>
                                          <li><i class="ti-trash"></i>Delete</li>
                                          <li><i class="fa fa-folder-open-o"></i>Archive</li>
                                          <li><i class="fa fa-ban"></i>Block</li>
                                          <li><i class="fa fa-eye-slash"></i>Ignore Message</li>
                                          <li><i class="fa fa-envelope"></i>Mark Unread</li>
                                       </ul>
                                    </div>
                                 </div>
                              </a>
                              @if(isset($message->uid))
                                 <input type="hidden" class="hidden-message-uid" value="{{ $message->uid }}">
                              @else
                                 <input type="hidden" class="hidden-message-uid" value="null">
                              @endif
                           </li>
                           @endforeach
                        @endforeach
                        
                     </ul>
                  </div>
               </div>
               <div class="tab-content messenger">
                  <div class="tab-pane active fade show " id="link1">
                     <div class="row merged">
                        <div class="col-lg-12">
                           <div class="mesg-area-head">
                              <div class="active-user">
                                 <figure><img src="/user/images/resources/friend-avatar3.jpg" alt="">
                                    <span class="status f-online"></span>
                                 </figure>
                                 <div>
                                    <h6 class="unread">Andrew</h6>
                                    <span>Online</span>
                                 </div>
                              </div>
                              <ul class="live-calls">
                                 <li class="audio-call"><span class="fa fa-phone"></span></li>
                                 <li class="video-call"><span class="fa fa-video"></span></li>
                                 <li class="uzr-info"><span class="fa fa-info-circle"></span></li>
                                 <li>
                                    <div class="dropdown">
                                       <button class="btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                          <i class="ti-view-grid"></i>
                                       </button>
                                       <div class="dropdown-menu dropdown-menu-right">
                                          <a class="dropdown-item audio-call" href="#"><i class="ti-headphone-alt"></i>Voice Call</a>
                                          <a href="#" class="dropdown-item video-call"><i class="ti-video-camera"></i>Video Call</a>
                                          <hr>
                                          <a href="#" class="dropdown-item"><i class="ti-server"></i>Clear History</a>
                                          <a href="#" class="dropdown-item"><i class="ti-hand-stop"></i>Block Contact</a>
                                          <a href="#" class="dropdown-item"><i class="ti-trash"></i>Delete Contact</a>
                                       </div>
                                    </div>
                                 </li>
                              </ul>
                           </div>
                        </div>
                        <div class="col-lg-8 col-md-8">
                           <div class="mesge-area">
                              <ul class="conversations">
                                 <li>
                                    <figure><img src="/user/images/resources/user1.jpg" alt=""></figure>
                                    <div class="text-box">
                                       <p>HI, i have faced a problem with your software. are you available now</p>
                                       <span><i class="ti-check"></i><i class="ti-check"></i> 2:32PM</span>
                                    </div>
                                 </li>
                                 <li class="me">
                                    <figure><img src="/user/images/resources/user2.jpg" alt=""></figure>
                                    <div class="text-box">
                                       <p>HI, i have checked about your query, there is no any problem like that...</p>
                                       <span><i class="ti-check"></i><i class="ti-check"></i> 2:35PM</span>
                                    </div>
                                 </li>
                                 <li class="you">
                                    <figure><img src="/user/images/resources/user1.jpg" alt=""></figure>
                                    <div class="text-box">
                                       <p>
                                          thank you for your quick reply, i am sending you a screenshot
                                          <img src="/user/images/resources/screenshot-messenger.jpg" alt="">
                                          <em>Size: 106kb <ins>download Complete</ins></em>
                                       </p>
                                       <span><i class="ti-check"></i><i class="ti-check"></i> 2:36PM</span>
                                    </div>
                                 </li>
                                 <li class="me">
                                    <figure><img src="/user/images/resources/user2.jpg" alt=""></figure>
                                    <div class="text-box">
                                       <p>Yes, i have to see, please follow the below link.. <a href="#" title="">https://www.abc.com</a></p>
                                       <span><i class="ti-check"></i><i class="ti-check"></i> 2:38PM</span>
                                    </div>
                                 </li>
                                 <li class="me">
                                    <figure><img src="/user/images/resources/user2.jpg" alt=""></figure>
                                    <div class="text-box">
                                       <p>
                                          Dear You May again download the package directly..
                                          <span><ins>File.txt</ins> <i class="fa fa-file"></i> 30MB download complete</span>
                                       </p>
                                       <span><i class="ti-check"></i><i class="ti-check"></i> 2:40PM</span>
                                    </div>
                                 </li>
                                 <li class="you">
                                    <figure><img src="/user/images/resources/user1.jpg" alt=""></figure>
                                    <div class="text-box">
                                       <div class="wave">
                                          <span class="dot"></span>
                                          <span class="dot"></span>
                                          <span class="dot"></span>
                                       </div>
                                    </div>
                                 </li>
                              </ul>
                           </div>
                           <div class="message-writing-box">
                              <form method="post">
                                 <div class="text-area">
                                    <input type="text" placeholder="write your message here..">
                                    <button type="submit"><i class="fa fa-paper-plane-o"></i></button>
                                 </div>
                                 <div class="emojies">
                                    <i><img src="/user/images/smiles/happy-3.png" alt=""></i>
                                    <ul class="emojies-list">
                                       <li><a href="#" title=""><img src="/user/images/smiles/unhappy.png" alt=""></a></li>
                                       <li><a href="#" title=""><img src="/user/images/smiles/tongue-out-1.png" alt=""></a></li>
                                       <li><a href="#" title=""><img src="/user/images/smiles/suspicious.png" alt=""></a></li>
                                       <li><a href="#" title=""><img src="/user/images/smiles/smiling.png" alt=""></a></li>
                                       <li><a href="#" title=""><img src="/user/images/smiles/wink.png" alt=""></a></li>
                                       <li><a href="#" title=""><img src="/user/images/smiles/bored.png" alt=""></a></li>
                                       <li><a href="#" title=""><img src="/user/images/smiles/angry-1.png" alt=""></a></li>
                                       <li><a href="#" title=""><img src="/user/images/smiles/angry.png" alt=""></a></li>
                                       <li><a href="#" title=""><img src="/user/images/smiles/bored-1.png" alt=""></a></li>
                                       <li><a href="#" title=""><img src="/user/images/smiles/bored-2.png" alt=""></a></li>
                                       <li><a href="#" title=""><img src="/user/images/smiles/confused-1.png" alt=""></a></li>
                                       <li><a href="#" title=""><img src="/user/images/smiles/confused.png" alt=""></a></li>
                                       <li><a href="#" title=""><img src="/user/images/smiles/crying-1.png" alt=""></a></li>
                                       <li><a href="#" title=""><img src="/user/images/smiles/crying.png" alt=""></a></li>
                                       <li><a href="#" title=""><img src="/user/images/smiles/embarrassed.png" alt=""></a></li>
                                       <li><a href="#" title=""><img src="/user/images/smiles/emoticons.png" alt=""></a></li>
                                       <li><a href="#" title=""><img src="/user/images/smiles/happy-1.png" alt=""></a></li>
                                       <li><a href="#" title=""><img src="/user/images/smiles/happy-2.png" alt=""></a></li>
                                       <li><a href="#" title=""><img src="/user/images/smiles/happy-3.png" alt=""></a></li>
                                       <li><a href="#" title=""><img src="/user/images/smiles/happy-4.png" alt=""></a></li>
                                       <li><a href="#" title=""><img src="/user/images/smiles/ill.png" alt=""></a></li>
                                       <li><a href="#" title=""><img src="/user/images/smiles/in-love.png" alt=""></a></li>
                                       <li><a href="#" title=""><img src="/user/images/smiles/kissing.png" alt=""></a></li>
                                       <li><a href="#" title=""><img src="/user/images/smiles/mad.png" alt=""></a></li>
                                       <li><a href="#" title=""><img src="/user/images/smiles/nerd.png" alt=""></a></li>
                                       <li><a href="#" title=""><img src="/user/images/smiles/ninja.png" alt=""></a></li>
                                       <li><a href="#" title=""><img src="/user/images/smiles/quiet.png" alt=""></a></li>
                                       <li><a href="#" title=""><img src="/user/images/smiles/sad.png" alt=""></a></li>
                                       <li><a href="#" title=""><img src="/user/images/smiles/secret.png" alt=""></a></li>
                                       <li><a href="#" title=""><img src="/user/images/smiles/smile.png" alt=""></a></li>
                                       <li><a href="#" title=""><img src="/user/images/smiles/surprised-1.png" alt=""></a></li>
                                       <li><a href="#" title=""><img src="/user/images/smiles/tongue-out.png" alt=""></a></li>
                                       <li><a href="#" title=""><img src="/user/images/smiles/unhappy.png" alt=""></a></li>
                                       <li><a href="#" title=""><img src="/user/images/smiles/suspicious.png" alt=""></a></li>
                                    </ul>
                                 </div>
                                 <div class="attach-file">
                                    <label class="fileContainer">
                                       <i class="ti-clip"></i>
                                       <input type="file">
                                    </label>
                                 </div>
                              </form>
                           </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                           <div class="chater-info">
                              <figure><img src="/user/images/resources/chatuser1.jpg" alt=""></figure>
                              <h6>Andrew</h6>
                              <span>Online</span>
                              <div class="userabout">
                                 <span>About</span>
                                 <p>I love reading, traveling and discovering new things. You need to be happy in life.</p>
                                 <ul>
                                    <li><span>Phone:</span> +123976980</li>
                                    <li><span>Website:</span> <a href="#" title="">www.abc.com</a></li>
                                    <li><span>Email:</span> <a href="http://wpkixx.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="a0d3c1cdd0ccc5e0c7cdc1c9cc8ec3cfcd">[email&#160;protected]</a></li>
                                    <li><span>Phone:</span> Ontario, Canada</li>
                                 </ul>
                                 <div class="media">
                                    <span>Media</span>
                                    <ul>
                                       <li><img src="/user/images/resources/audio-user1.jpg" alt=""></li>
                                       <li><img src="/user/images/resources/audio-user2.jpg" alt=""></li>
                                       <li><img src="/user/images/resources/audio-user3.jpg" alt=""></li>
                                       <li><img src="/user/images/resources/audio-user4.jpg" alt=""></li>
                                       <li><img src="/user/images/resources/audio-user5.jpg" alt=""></li>
                                       <li><img src="/user/images/resources/audio-user6.jpg" alt=""></li>
                                       <li><img src="/user/images/resources/admin2.jpg" alt=""></li>
                                       <li><img src="/user/images/resources/audio-user1.jpg" alt=""></li>
                                       <li><img src="/user/images/resources/audio-user4.jpg" alt=""></li>
                                       <li><img src="/user/images/resources/audio-user3.jpg" alt=""></li>
                                    </ul>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  

               </div>
            </div>
         </div>
      </div>
   </div>
</section><!-- content -->

@endsection