<div class="central-meta postbox">
    <div style="display: flex; align-items: center;">
        <span class="create-post">Create post
            <a class="reload-post-btn" style="margin-left: auto;">
                <i class="fa fa-refresh"></i> Reload Post
            </a>
        </span>
    </div>
    <div class="new-postbox">

        <figure>
            <img src="{{ $data['account']->avatar  ??  '' }}" alt="" style="height: auto;max-width: 10%">
        </figure>
        <div class="newpst-input">
            <form method="post" action="{{ route('profile.postStatus', ['uid' => $data['account']->uid]) }}">
                @csrf
                <textarea name="status" rows="2" placeholder="Share some what you are thinking?"></textarea>
                <div class="attachments">
                    <ul>
                        <li>
                            <span class="add-loc">
                                <i class="fa fa-map-marker"></i>
                            </span>
                        </li>
                        <li>
                            <i class="fa fa-music"></i>
                            <label class="fileContainer">
                                <input type="file" name="music">
                            </label>
                        </li>
                        <li>
                            <i class="fa fa-image"></i>
                            <label class="fileContainer">
                                <input type="file" name="image">
                            </label>
                        </li>
                        <li>
                            <i class="fa fa-video-camera"></i>
                            <label class="fileContainer">
                                <input type="file" name="video">
                            </label>
                        </li>
                        <li>
                            <i class="fa fa-camera"></i>
                            <label class="fileContainer">
                                <input type="file" name="camera">
                            </label>
                        </li>
                        <li class="preview-btn">
                            <button class="post-btn-preview" type="submit" data-ripple="">Preview</button>
                        </li>
                    </ul>
                    <button class="post-btn" type="submit" data-ripple="">Post</button>
                </div>
                <input type="hidden" name="latitude" id="us3-lat">
                <input type="hidden" name="longitude" id="us3-lon">
            </form>
        </div>
        <div class="add-location-post" style="display: none;">
            <span>Drag map point to selected area</span>
            <div class="row">
                <div class="col-lg-6">
                    <label class="control-label">Lat :</label>
                    <input type="text" id="us3-lat" readonly />
                </div>
                <div class="col-lg-6">
                    <label>Long :</label>
                    <input type="text" id="us3-lon" readonly />
                </div>
            </div>
            <!-- map -->
            <div id="us3" style="height: 300px;"></div>
        </div>
    </div>
</div><!-- add post new box -->