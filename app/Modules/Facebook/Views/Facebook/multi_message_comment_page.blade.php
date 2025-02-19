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


@endsection

@section('content')


<section>

   <div class="gap2 no-gap gray-bg">
      <div class="container-fluid no-padding">
         <div class="row">
            <div class="col-lg-12">
               <div class="message-users">
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
                   
                  <div class="mesg-peple">
                     <ul class="nav nav-tabs nav-tabs--vertical msg-pepl-list">
                        <li class="nav-item unread">
                           <a class="active" href="#link1" data-toggle="tab">
                              <figure><img src="/user/images/resources/friend-avatar3.jpg" alt="">
                                 <span class="status f-online"></span>
                              </figure>
                              <div class="user-name">
                                 <h6 class="">Andrew</h6>
                                 <span>you send a video - 2hrs ago</span>
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
                        </li>
                        <li class="nav-item ">
                           <a class="" href="#link2" data-toggle="tab">
                              <figure><img src="/user/images/resources/admin.jpg" alt="">
                                 <span class="status f-away"></span>
                              </figure>
                              <div class="user-name">
                                 <h6 class="unread">Jack Carter</h6>
                                 <span>you send a audio - Tue</span>
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

                        </li>
                        <li class="nav-item ">
                           <a class="" href="#link3" data-toggle="tab">
                              <figure><img src="/user/images/resources/friend-avatar4.jpg" alt="">
                                 <span class="status f-online"></span>
                              </figure>
                              <div class="user-name">
                                 <h6 class="unread">Julie Robert</h6>
                                 <span>hi, i am julie - wed</span>
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

                        </li>
                        <li class="nav-item ">
                           <a class="" href="#link4" data-toggle="tab">
                              <figure><img src="/user/images/resources/friend-avatar5.jpg" alt="">
                                 <span class="status f-offline"></span>
                              </figure>
                              <div class="user-name">
                                 <h6 class="unread">Jhon Doe</h6>
                                 <span>May i come to.. - Thr</span>
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

                        </li>
                        <li class="nav-item unread ">
                           <a class="" href="#link5" data-toggle="tab">
                              <figure><img src="/user/images/resources/friend-avatar6.jpg" alt="">
                                 <span class="status f-online"></span>
                              </figure>
                              <div class="user-name">
                                 <h6 class="unread">Single Men</h6>
                                 <span>hello? - a days ago</span>
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

                        </li>
                        <li class="nav-item ">
                           <a class="" href="#link6" data-toggle="tab">
                              <figure><img src="/user/images/resources/friend-avatar7.jpg" alt="">
                                 <span class="status f-offline"></span>
                              </figure>
                              <div class="user-name">
                                 <h6 class="unread">Sarah Jane</h6>
                                 <span>she send a video - a days ago</span>
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

                        </li>
                        <li class="nav-item ">
                           <a class="" href="#link7" data-toggle="tab">
                              <figure><img src="/user/images/resources/friend-avatar8.jpg" alt="">
                                 <span class="status f-online"></span>
                              </figure>
                              <div class="user-name">
                                 <h6 class="">Julie Robert</h6>
                                 <span>She send a file - 22 jan</span>
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

                        </li>
                        <li class="nav-item unread ">
                           <a class="" href="#link8" data-toggle="tab">
                              <figure><img src="/user/images/resources/friend-avatar6.jpg" alt="">
                                 <span class="status f-away"></span>
                              </figure>
                              <div class="user-name">
                                 <h6 class="unread">Frank Will</h6>
                                 <span>You there ? - a days ago</span>
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

                        </li>
                        <li class="nav-item ">
                           <a class="" href="#link9" data-toggle="tab">
                              <figure><img src="/user/images/resources/friend-avatar9.jpg" alt="">
                                 <span class="status f-online"></span>
                              </figure>
                              <div class="user-name">
                                 <h6 class="unread">Niclos Cage</h6>
                                 <span>you send a video - wed</span>
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

                        </li>
                        <li class="nav-item ">
                           <a class="" href="#link10" data-toggle="tab">
                              <figure><img src="/user/images/resources/friend-avatar8.jpg" alt="">
                                 <span class="status f-offline"></span>
                              </figure>
                              <div class="user-name">
                                 <h6 class="">kelly Quin</h6>
                                 <span>Hi dude.. - 23 feb</span>
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

                        </li>
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
                  <div class="tab-pane fade" id="link2">
                     <div class="row merged">
                        <div class="col-lg-12">
                           <div class="mesg-area-head">
                              <div class="active-user">
                                 <figure><img src="/user/images/resources/admin.jpg" alt="">
                                    <span class="status f-away"></span>
                                 </figure>
                                 <div>
                                    <h6 class="unread">Jack Carter</h6>
                                    <span>Away</span>
                                 </div>
                              </div>
                              <ul class="live-calls">
                                 <li><span class="fa fa-phone"></span></li>
                                 <li><span class="fa fa-video"></span></li>
                                 <li><span class="fa fa-info-circle"></span></li>
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
                        <div class="col-lg-8">
                           <div class="mesge-area conversations">
                              <div class="empty-chat">
                                 <div class="no-messages">
                                    <i class="ti-comments"></i>
                                    <p>Seems people are shy to start the chat. Break the ice send the first message.</p>
                                 </div>
                              </div>
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
                        <div class="col-lg-4">
                           <div class="chater-info">
                              <figure><img src="/user/images/resources/author.jpg" alt=""></figure>
                              <h6>Jack Carter</h6>
                              <span>Active a days ago</span>
                              <div class="userabout">
                                 <span>About</span>
                                 <p>I love reading, traveling and discovering new things. You need to be happy in life.</p>
                                 <ul>
                                    <li><span>Phone:</span> +123976980</li>
                                    <li><span>Website:</span> <a href="#" title="">www.abc.com</a></li>
                                    <li><span>Email:</span> <a href="http://wpkixx.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="ee9d8f839e828bae89838f8782c08d8183">[email&#160;protected]</a></li>
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
                  <div class="tab-pane fade" id="link3">
                     <div class="row merged">
                        <div class="col-lg-12">
                           <div class="mesg-area-head">
                              <div class="active-user">
                                 <figure><img src="/user/images/resources/friend-avatar4.jpg" alt="">
                                    <span class="status f-online"></span>
                                 </figure>
                                 <div>
                                    <h6 class="unread">Julie Robert</h6>
                                    <span>Online</span>
                                 </div>
                              </div>
                              <ul class="live-calls">
                                 <li><span class="fa fa-phone"></span></li>
                                 <li><span class="fa fa-video"></span></li>
                                 <li><span class="fa fa-info-circle"></span></li>
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
                        <div class="col-lg-8">
                           <div class="mesge-area conversations">
                              <div class="empty-chat">
                                 <div class="no-messages">
                                    <i class="ti-comments"></i>
                                    <p>Seems people are shy to start the chat. Break the ice send the first message.</p>
                                 </div>
                              </div>
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
                        <div class="col-lg-4">
                           <div class="chater-info">
                              <figure><img src="/user/images/resources/chatuser3.jpg" alt=""></figure>
                              <h6>Julie Robert</h6>
                              <span>Active one hours ago</span>
                              <div class="userabout">
                                 <span>About</span>
                                 <p>I love reading, traveling and discovering new things. You need to be happy in life.</p>
                                 <ul>
                                    <li><span>Phone:</span> +123976980</li>
                                    <li><span>Website:</span> <a href="#" title="">www.abc.com</a></li>
                                    <li><span>Email:</span> <a href="http://wpkixx.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="c0b3a1adb0aca580a7ada1a9aceea3afad">[email&#160;protected]</a></li>
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
                  <div class="tab-pane fade" id="link4">
                     <div class="row merged">
                        <div class="col-lg-12">
                           <div class="mesg-area-head">
                              <div class="active-user">
                                 <figure><img src="/user/images/resources/friend-avatar5.jpg" alt="">
                                    <span class="status f-offline"></span>
                                 </figure>
                                 <div>
                                    <h6 class="unread">Jhon Doe</h6>
                                    <span>Offline</span>
                                 </div>
                              </div>
                              <ul class="live-calls">
                                 <li><span class="fa fa-phone"></span></li>
                                 <li><span class="fa fa-video"></span></li>
                                 <li><span class="fa fa-info-circle"></span></li>
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
                        <div class="col-lg-8">
                           <div class="mesge-area conversations">
                              <div class="empty-chat">
                                 <div class="no-messages">
                                    <i class="ti-comments"></i>
                                    <p>Seems people are shy to start the chat. Break the ice send the first message.</p>
                                 </div>
                              </div>
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
                        <div class="col-lg-4">
                           <div class="chater-info">
                              <figure><img src="/user/images/resources/chatuser2.jpg" alt=""></figure>
                              <h6>Jhon Doe</h6>
                              <span>Active 5 hours ago</span>
                              <div class="userabout">
                                 <span>About</span>
                                 <p>I love reading, traveling and discovering new things. You need to be happy in life.</p>
                                 <ul>
                                    <li><span>Phone:</span> +123976980</li>
                                    <li><span>Website:</span> <a href="#" title="">www.abc.com</a></li>
                                    <li><span>Email:</span> <a href="http://wpkixx.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="84f7e5e9f4e8e1c4e3e9e5ede8aae7ebe9">[email&#160;protected]</a></li>
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
                  <div class="tab-pane fade" id="link5">
                     <div class="row merged">
                        <div class="col-lg-12">
                           <div class="mesg-area-head">
                              <div class="active-user">
                                 <figure><img src="/user/images/resources/friend-avatar6.jpg" alt="">
                                    <span class="status f-online"></span>
                                 </figure>
                                 <div>
                                    <h6 class="unread">Single Men</h6>
                                    <span>Online</span>
                                 </div>
                              </div>
                              <ul class="live-calls">
                                 <li><span class="fa fa-phone"></span></li>
                                 <li><span class="fa fa-video"></span></li>
                                 <li><span class="fa fa-info-circle"></span></li>
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
                        <div class="col-lg-8">
                           <div class="mesge-area conversations">
                              <div class="empty-chat">
                                 <div class="no-messages">
                                    <i class="ti-comments"></i>
                                    <p>Seems people are shy to start the chat. Break the ice send the first message.</p>
                                 </div>
                              </div>
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
                        <div class="col-lg-4">
                           <div class="chater-info">
                              <figure><img src="/user/images/resources/chatuser4.jpg" alt=""></figure>
                              <h6>Single Men</h6>
                              <span>Active 2 hours ago</span>
                              <div class="userabout">
                                 <span>About</span>
                                 <p>I love reading, traveling and discovering new things. You need to be happy in life.</p>
                                 <ul>
                                    <li><span>Phone:</span> +123976980</li>
                                    <li><span>Website:</span> <a href="#" title="">www.abc.com</a></li>
                                    <li><span>Email:</span> <a href="http://wpkixx.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="f083919d809c95b0979d91999cde939f9d">[email&#160;protected]</a></li>
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
                  <div class="tab-pane fade" id="link6">
                     <div class="row merged">
                        <div class="col-lg-12">
                           <div class="mesg-area-head">
                              <div class="active-user">
                                 <figure><img src="/user/images/resources/friend-avatar7.jpg" alt="">
                                    <span class="status f-offline"></span>
                                 </figure>
                                 <div>
                                    <h6 class="unread">Sarah Jane</h6>
                                    <span>Offline</span>
                                 </div>
                              </div>
                              <ul class="live-calls">
                                 <li><span class="fa fa-phone"></span></li>
                                 <li><span class="fa fa-video"></span></li>
                                 <li><span class="fa fa-info-circle"></span></li>
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
                        <div class="col-lg-8">
                           <div class="mesge-area conversations">
                              <div class="empty-chat">
                                 <div class="no-messages">
                                    <i class="ti-comments"></i>
                                    <p>Seems people are shy to start the chat. Break the ice send the first message.</p>
                                 </div>
                              </div>
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
                        <div class="col-lg-4">
                           <div class="chater-info">
                              <figure><img src="/user/images/resources/chatuser5.jpg" alt=""></figure>
                              <h6>Sarah Jane</h6>
                              <span>Active 2 hours ago</span>
                              <div class="userabout">
                                 <span>About</span>
                                 <p>I love reading, traveling and discovering new things. You need to be happy in life.</p>
                                 <ul>
                                    <li><span>Phone:</span> +123976980</li>
                                    <li><span>Website:</span> <a href="#" title="">www.abc.com</a></li>
                                    <li><span>Email:</span> <a href="http://wpkixx.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="f281939f829e97b2959f939b9edc919d9f">[email&#160;protected]</a></li>
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
                  <div class="tab-pane fade" id="link7">
                     <div class="row merged">
                        <div class="col-lg-12">
                           <div class="mesg-area-head">
                              <div class="active-user">
                                 <figure><img src="/user/images/resources/friend-avatar8.jpg" alt="">
                                    <span class="status f-online"></span>
                                 </figure>
                                 <div>
                                    <h6 class="unread">Julie Robert</h6>
                                    <span>Online</span>
                                 </div>
                              </div>
                              <ul class="live-calls">
                                 <li><span class="fa fa-phone"></span></li>
                                 <li><span class="fa fa-video"></span></li>
                                 <li><span class="fa fa-info-circle"></span></li>
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
                        <div class="col-lg-8">
                           <div class="mesge-area conversations">
                              <div class="empty-chat">
                                 <div class="no-messages">
                                    <i class="ti-comments"></i>
                                    <p>Seems people are shy to start the chat. Break the ice send the first message.</p>
                                 </div>
                              </div>
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
                        <div class="col-lg-4">
                           <div class="chater-info">
                              <figure><img src="/user/images/resources/chatuser6.jpg" alt=""></figure>
                              <h6>Julie Robert</h6>
                              <span>Active 2 days ago</span>
                              <div class="userabout">
                                 <span>About</span>
                                 <p>I love reading, traveling and discovering new things. You need to be happy in life.</p>
                                 <ul>
                                    <li><span>Phone:</span> +123976980</li>
                                    <li><span>Website:</span> <a href="#" title="">www.abc.com</a></li>
                                    <li><span>Email:</span> <a href="http://wpkixx.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="f685979b869a93b6919b979f9ad895999b">[email&#160;protected]</a></li>
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
                  <div class="tab-pane fade" id="link8">
                     <div class="row merged">
                        <div class="col-lg-12">
                           <div class="mesg-area-head">
                              <div class="active-user">
                                 <figure><img src="/user/images/resources/friend-avatar6.jpg" alt="">
                                    <span class="status f-away"></span>
                                 </figure>
                                 <div>
                                    <h6 class="unread">Frank Will</h6>
                                    <span>Away</span>
                                 </div>
                              </div>
                              <ul class="live-calls">
                                 <li><span class="fa fa-phone"></span></li>
                                 <li><span class="fa fa-video"></span></li>
                                 <li><span class="fa fa-info-circle"></span></li>
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
                        <div class="col-lg-8">
                           <div class="mesge-area conversations">
                              <div class="empty-chat">
                                 <div class="no-messages">
                                    <i class="ti-comments"></i>
                                    <p>Seems people are shy to start the chat. Break the ice send the first message.</p>
                                 </div>
                              </div>
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
                        <div class="col-lg-4">
                           <div class="chater-info">
                              <figure><img src="/user/images/resources/chatuser4.jpg" alt=""></figure>
                              <h6>Frank Will</h6>
                              <span>Active 2 months ago</span>
                              <div class="userabout">
                                 <span>About</span>
                                 <p>I love reading, traveling and discovering new things. You need to be happy in life.</p>
                                 <ul>
                                    <li><span>Phone:</span> +123976980</li>
                                    <li><span>Website:</span> <a href="#" title="">www.abc.com</a></li>
                                    <li><span>Email:</span> <a href="http://wpkixx.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="5625373b263a3316313b373f3a7835393b">[email&#160;protected]</a></li>
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
                  <div class="tab-pane fade" id="link9">
                     <div class="row merged">
                        <div class="col-lg-12">
                           <div class="mesg-area-head">
                              <div class="active-user">
                                 <figure><img src="/user/images/resources/friend-avatar9.jpg" alt="">
                                    <span class="status f-online"></span>
                                 </figure>
                                 <div>
                                    <h6 class="unread">Niclos Cage</h6>
                                    <span>Online</span>
                                 </div>
                              </div>
                              <ul class="live-calls">
                                 <li><span class="fa fa-phone"></span></li>
                                 <li><span class="fa fa-video"></span></li>
                                 <li><span class="fa fa-info-circle"></span></li>
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
                        <div class="col-lg-8">
                           <div class="mesge-area conversations">
                              <div class="empty-chat">
                                 <div class="no-messages">
                                    <i class="ti-comments"></i>
                                    <p>Seems people are shy to start the chat. Break the ice send the first message.</p>
                                 </div>
                              </div>
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
                        <div class="col-lg-4">
                           <div class="chater-info">
                              <figure><img src="/user/images/resources/chatuser8.jpg" alt=""></figure>
                              <h6>Niclos Cage</h6>
                              <span>Active 10 hours ago</span>
                              <div class="userabout">
                                 <span>About</span>
                                 <p>I love reading, traveling and discovering new things. You need to be happy in life.</p>
                                 <ul>
                                    <li><span>Phone:</span> +123976980</li>
                                    <li><span>Website:</span> <a href="#" title="">www.abc.com</a></li>
                                    <li><span>Email:</span> <a href="http://wpkixx.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="6714060a170b0227000a060e0b4904080a">[email&#160;protected]</a></li>
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
                  <div class="tab-pane fade" id="link10">
                     <div class="row merged">
                        <div class="col-lg-12">
                           <div class="mesg-area-head">
                              <div class="active-user">
                                 <figure><img src="/user/images/resources/friend-avatar8.jpg" alt="">
                                    <span class="status f-offline"></span>
                                 </figure>
                                 <div>
                                    <h6 class="unread">Kelly Quin</h6>
                                    <span>Offline</span>
                                 </div>
                              </div>
                              <ul class="live-calls">
                                 <li><span class="fa fa-phone"></span></li>
                                 <li><span class="fa fa-video"></span></li>
                                 <li><span class="fa fa-info-circle"></span></li>
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
                        <div class="col-lg-8">
                           <div class="mesge-area conversations">
                              <div class="empty-chat">
                                 <div class="no-messages">
                                    <i class="ti-comments"></i>
                                    <p>Seems people are shy to start the chat. Break the ice send the first message.</p>
                                 </div>
                              </div>
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
                        <div class="col-lg-4">
                           <div class="chater-info">
                              <figure><img src="/user/images/resources/chatuser6.jpg" alt=""></figure>
                              <h6>Kelly Quin</h6>
                              <span>Active 10 hours ago</span>
                              <div class="userabout">
                                 <span>About</span>
                                 <p>I love reading, traveling and discovering new things. You need to be happy in life.</p>
                                 <ul>
                                    <li><span>Phone:</span> +123976980</li>
                                    <li><span>Website:</span> <a href="#" title="">www.abc.com</a></li>
                                    <li><span>Email:</span> <a href="http://wpkixx.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="8efdefe3fee2ebcee9e3efe7e2a0ede1e3">[email&#160;protected]</a></li>
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

