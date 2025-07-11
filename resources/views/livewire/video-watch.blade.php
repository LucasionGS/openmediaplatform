<div class="max-w-7xl mx-auto">
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 {{ $isSharedView ? '' : 'lg:grid-cols-3' }} gap-6">
        <!-- Main Video Section -->
        <div class="{{ $isSharedView ? '' : 'lg:col-span-2' }}">
            <!-- Video Player -->
            <div class="bg-black rounded-lg overflow-hidden">
                <livewire:video-player 
                    :video="$isSharedView ? null : $video"
                    :video-src="$isSharedView ? route('videos.share.raw', ['token' => $video->share_token]) : route('videos.raw', $video)"
                    :video-title="$video->title"
                    :poster="$isSharedView ? route('videos.share.thumbnail', ['token' => $video->share_token]) : $video->getThumbnailUrl()"
                    :autoplay="false"
                    :muted="false" />
            </div>

            <!-- Video Information -->
            <div class="mt-4">
                <h1 class="text-xl font-bold text-gray-900 mb-2">{{ $video->title }}</h1>
                
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center text-sm text-gray-600 space-x-2">
                        <span>{{ $video->getFormattedViews() }}</span>
                        <span>•</span>
                        <span>{{ $video->getTimeAgo() }}</span>
                    </div>
                    
                    <!-- Engagement Buttons -->
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center bg-gray-100 rounded-full">
                            <button wire:click="toggleLike" 
                                    class="flex items-center space-x-2 px-4 py-2 rounded-l-full hover:bg-gray-200 transition-colors
                                           {{ $userEngagement && $userEngagement->engagement_type === 'like' ? 'text-blue-600' : '' }}">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M7.493 18.75c-.425 0-.82-.236-.975-.632A7.48 7.48 0 016 15.375c0-1.75.599-3.358 1.602-4.634.151-.192.373-.309.6-.397.473-.183.89-.514 1.212-.924a9.042 9.042 0 012.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 00.322-1.672V3a.75.75 0 01.75-.75 2.25 2.25 0 012.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558-.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 01-2.649 7.521c-.388.482-.987.729-1.605.729H14.23c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 00-1.423-.23h-.777zM2.331 10.977a11.969 11.969 0 00-.831 4.398 12 12 0 00.52 3.507c.26.85 1.084 1.368 1.973 1.368H4.9c.445 0 .72-.498.523-.898a8.963 8.963 0 01-.924-3.977c0-1.708.476-3.305 1.302-4.666.245-.403-.028-.959-.5-.959H4.25c-.832 0-1.612.453-1.918 1.227z"/>
                                </svg>
                                <span>{{ number_format($video->likes) }}</span>
                            </button>
                            
                            <div class="w-px bg-gray-300 h-6"></div>
                            
                            <button wire:click="toggleDislike" 
                                    class="flex items-center space-x-2 px-4 py-2 rounded-r-full hover:bg-gray-200 transition-colors
                                           {{ $userEngagement && $userEngagement->engagement_type === 'dislike' ? 'text-red-600' : '' }}">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" transform="rotate(180)">
                                    <path d="M7.493 18.75c-.425 0-.82-.236-.975-.632A7.48 7.48 0 016 15.375c0-1.75.599-3.358 1.602-4.634.151-.192.373-.309.6-.397.473-.183.89-.514 1.212-.924a9.042 9.042 0 012.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 00.322-1.672V3a.75.75 0 01.75-.75 2.25 2.25 0 012.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558-.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 01-2.649 7.521c-.388.482-.987.729-1.605.729H14.23c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 00-1.423-.23h-.777zM2.331 10.977a11.969 11.969 0 00-.831 4.398 12 12 0 00.52 3.507c.26.85 1.084 1.368 1.973 1.368H4.9c.445 0 .72-.498.523-.898a8.963 8.963 0 01-.924-3.977c0-1.708.476-3.305 1.302-4.666.245-.403-.028-.959-.5-.959H4.25c-.832 0-1.612.453-1.918 1.227z"/>
                                </svg>
                                <span>{{ number_format($video->dislikes) }}</span>
                            </button>
                        </div>
                        
                        <button wire:click="openShareModal" 
                                class="flex items-center space-x-2 px-4 py-2 bg-gray-100 rounded-full hover:bg-gray-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                            </svg>
                            <span>Share</span>
                        </button>

                        @auth
                            <x-add-to-playlist :video-id="$video->vid" />
                        @endauth

                        @auth
                            @if(auth()->id() === $video->user_id || auth()->user()->canModerateContent())
                                <a href="{{ route('videos.edit', $video) }}" 
                                   class="flex items-center space-x-2 px-4 py-2 bg-blue-100 text-blue-700 rounded-full hover:bg-blue-200 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    <span>{{ auth()->id() === $video->user_id ? 'Edit' : 'Moderate' }}</span>
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>

                <!-- Channel Information -->
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('channel.show', $video->user) }}">
                            @if($video->user->profile_picture)
                                <img src="{{ asset('sf/' . $video->user->profile_picture) }}" 
                                     alt="{{ $video->user->getChannelName() }}" 
                                     class="w-12 h-12 rounded-full object-cover hover:opacity-80 transition-opacity">
                            @else
                                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white text-lg font-semibold hover:bg-blue-600 transition-colors">
                                    {{ substr($video->user->getChannelName(), 0, 1) }}
                                </div>
                            @endif
                        </a>
                        <div>
                            <a href="{{ route('channel.show', $video->user) }}" class="hover:text-blue-600">
                                <h3 class="font-semibold text-gray-900">{{ $video->user->getChannelName() }}</h3>
                            </a>
                            <p class="text-sm text-gray-600">{{ number_format($video->user->subscribers_count) }} subscribers</p>
                        </div>
                    </div>
                    
                    @auth
                        @if($video->user_id !== auth()->id())
                            <button wire:click="toggleSubscription" 
                                    class="px-6 py-2 rounded-full transition-colors font-medium
                                           {{ $isSubscribed 
                                              ? 'bg-gray-200 text-gray-700 hover:bg-gray-300' 
                                              : 'bg-red-600 text-white hover:bg-red-700' }}">
                                {{ $isSubscribed ? 'Subscribed' : 'Subscribe' }}
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="px-6 py-2 bg-red-600 text-white rounded-full hover:bg-red-700 transition-colors">
                            Subscribe
                        </a>
                    @endauth
                </div>

                <!-- Video Description -->
                @if($video->description)
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $video->description }}</p>
                    </div>
                @endif

                <!-- Comments Section -->
                <div class="mt-8">
                    <div class="flex items-center space-x-4 mb-6">
                        <h3 class="text-lg font-semibold">{{ number_format($video->comments) }} Comments</h3>
                    </div>

                    <!-- Add Comment Form -->
                    @auth
                        <!-- Success/Error Messages -->
                        @if (session()->has('comment_success'))
                            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
                                {{ session('comment_success') }}
                            </div>
                        @endif
                        
                        @if (session()->has('reply_success'))
                            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
                                {{ session('reply_success') }}
                            </div>
                        @endif

                        <form wire:submit.prevent="addComment" class="mb-6">
                            <div class="flex space-x-3">
                                @if(auth()->user()->profile_picture)
                                    <img src="{{ asset('sf/' . auth()->user()->profile_picture) }}" 
                                         alt="{{ auth()->user()->name }}" 
                                         class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <textarea wire:model.defer="newComment" 
                                              placeholder="Add a comment..."
                                              class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 resize-none"
                                              rows="3"
                                              oninput="toggleCommentButton(this)"></textarea>
                                    @error('newComment')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <div class="flex justify-end space-x-2 mt-2">
                                        <button type="button" 
                                                wire:click="$set('newComment', '')"
                                                class="px-4 py-2 text-gray-600 hover:text-gray-800"
                                                onclick="document.getElementById('commentSubmitBtn').disabled = true;">
                                            Cancel
                                        </button>
                                        <button type="submit" 
                                                id="commentSubmitBtn"
                                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50"
                                                wire:loading.attr="disabled"
                                                wire:target="addComment"
                                                disabled>
                                            <span wire:loading.remove wire:target="addComment">Comment</span>
                                            <span wire:loading wire:target="addComment">Posting...</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg text-center">
                            <p class="text-gray-600">
                                <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Sign in</a> to add a comment
                            </p>
                        </div>
                    @endauth

                    <!-- Comments List -->
                    <div class="space-y-6">
                        @foreach($comments as $comment)
                            <div class="flex space-x-3">
                                @if($comment->user->profile_picture)
                                    <img src="{{ asset('sf/' . $comment->user->profile_picture) }}" 
                                         alt="{{ $comment->user->name }}" 
                                         class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                        {{ substr($comment->user->name, 0, 1) }}
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-semibold text-sm">{{ $comment->user->name }}</span>
                                        <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-gray-700 mt-1">{{ $comment->content }}</p>
                                    
                                    <div class="flex items-center space-x-4 mt-2">
                                        <button class="flex items-center space-x-1 text-sm text-gray-600 hover:text-gray-800">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M7.493 18.75c-.425 0-.82-.236-.975-.632A7.48 7.48 0 016 15.375c0-1.75.599-3.358 1.602-4.634.151-.192.373-.309.6-.397.473-.183.89-.514 1.212-.924a9.042 9.042 0 012.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 00.322-1.672V3a.75.75 0 01.75-.75 2.25 2.25 0 012.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558-.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 01-2.649 7.521c-.388.482-.987.729-1.605.729H14.23c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 00-1.423-.23h-.777zM2.331 10.977a11.969 11.969 0 00-.831 4.398 12 12 0 00.52 3.507c.26.85 1.084 1.368 1.973 1.368H4.9c.445 0 .72-.498.523-.898a8.963 8.963 0 01-.924-3.977c0-1.708.476-3.305 1.302-4.666.245-.403-.028-.959-.5-.959H4.25c-.832 0-1.612.453-1.918 1.227z"/>
                                            </svg>
                                            <span>{{ $comment->likes }}</span>
                                        </button>
                                        
                                        @auth
                                            <button wire:click="setReplyTo({{ $comment->id }})" 
                                                    class="text-sm text-gray-600 hover:text-gray-800">
                                                Reply
                                            </button>
                                        @endauth
                                    </div>

                                    <!-- Reply Form -->
                                    @if($replyTo === $comment->id)
                                        <form wire:submit.prevent="addReply" class="mt-3">
                                            <div class="flex space-x-3">
                                                @if(auth()->user()->profile_picture)
                                                    <img src="{{ asset('sf/' . auth()->user()->profile_picture) }}" 
                                                         alt="{{ auth()->user()->name }}" 
                                                         class="w-8 h-8 rounded-full object-cover">
                                                @else
                                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-semibold">
                                                        {{ substr(auth()->user()->name, 0, 1) }}
                                                    </div>
                                                @endif
                                                <div class="flex-1">
                                                    <textarea wire:model.defer="replyContent" 
                                                              placeholder="Add a reply..."
                                                              class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 resize-none"
                                                              rows="2"
                                                              oninput="toggleReplyButton(this, {{ $comment->id }})"></textarea>
                                                    @error('replyContent')
                                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                    @enderror
                                                    <div class="flex justify-end space-x-2 mt-2">
                                                        <button type="button" 
                                                                wire:click="cancelReply"
                                                                class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800"
                                                                onclick="document.getElementById('replySubmitBtn{{ $comment->id }}').disabled = true;">
                                                            Cancel
                                                        </button>
                                                        <button type="submit" 
                                                                id="replySubmitBtn{{ $comment->id }}"
                                                                class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700"
                                                                wire:loading.attr="disabled"
                                                                wire:target="addReply"
                                                                disabled>
                                                            <span wire:loading.remove wire:target="addReply">Reply</span>
                                                            <span wire:loading wire:target="addReply">Posting...</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    @endif

                                    <!-- Replies -->
                                    @if($comment->replies->count() > 0)
                                        <div class="mt-4 ml-6 space-y-3">
                                            @foreach($comment->replies as $reply)
                                                <div class="flex space-x-3">
                                                    @if($reply->user->profile_picture)
                                                        <img src="{{ asset('sf/' . $reply->user->profile_picture) }}" 
                                                             alt="{{ $reply->user->name }}" 
                                                             class="w-8 h-8 rounded-full object-cover">
                                                    @else
                                                        <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center text-white text-xs font-semibold">
                                                            {{ substr($reply->user->name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                    <div class="flex-1">
                                                        <div class="flex items-center space-x-2">
                                                            <span class="font-semibold text-sm">{{ $reply->user->name }}</span>
                                                            <span class="text-xs text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                                                        </div>
                                                        <p class="text-gray-700 mt-1">{{ $reply->content }}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar - Related Videos (only show for authenticated users) -->
        @if(!$isSharedView)
            <div class="space-y-4">
                <h3 class="text-lg font-semibold">Related Videos</h3>
                
                @foreach($relatedVideos as $relatedVideo)
                    <div class="flex space-x-3 hover:bg-gray-50 p-2 rounded-lg">
                        <a href="{{ route('videos.show', $relatedVideo) }}" class="flex-shrink-0">
                            <div class="w-40 aspect-video bg-gray-100 rounded overflow-hidden">
                                <img src="{{ $relatedVideo->getThumbnailUrl() }}" 
                                     alt="{{ $relatedVideo->title }}" 
                                     class="w-full h-full object-cover">
                            </div>
                        </a>
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('videos.show', $relatedVideo) }}">
                                <h4 class="text-sm font-semibold text-gray-900 line-clamp-2 leading-5">
                                    {{ $relatedVideo->title }}
                                </h4>
                            </a>
                            <p class="text-xs text-gray-600 mt-1">{{ $relatedVideo->user->getChannelName() }}</p>
                            <div class="flex items-center text-xs text-gray-500 mt-1 space-x-1">
                                <span>{{ $relatedVideo->getFormattedViews() }}</span>
                                <span>•</span>
                                <span>{{ $relatedVideo->getTimeAgo() }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Share Modal -->
    @if($showShareModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click="closeShareModal">
            <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4" wire:click.stop>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Share Video</h3>
                    <button wire:click="closeShareModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="space-y-4">
                    <!-- Share Link Generation -->
                    @if(Auth::check() && Auth::id() === $video->user_id)
                        @if(!$video->is_shareable || !$video->share_token)
                            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                                <p class="text-sm text-yellow-800 mb-3">
                                    No share link has been generated for this video yet.
                                </p>
                                <button wire:click="generateShareLink" 
                                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                                    Generate Share Link
                                </button>
                            </div>
                        @else
                            <!-- Share URL -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Share Link</label>
                                <div class="flex">
                                    <input type="text" 
                                           id="shareUrl"
                                           value="{{ $video->getShareUrl() }}" 
                                           readonly
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md bg-gray-50 text-sm">
                                    <button onclick="copyToClipboard()" 
                                            class="px-4 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700 text-sm">
                                        Copy
                                    </button>
                                </div>
                                <div class="mt-2">
                                    <button wire:click="revokeShareLink" 
                                            class="px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-700 text-xs"
                                            onclick="return confirm('Are you sure you want to revoke this share link? This will make it inaccessible to anyone who has it.')">
                                        Revoke Link
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Social Share Buttons -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Share On</label>
                                <div class="flex space-x-2">
                                    <!-- Twitter Share -->
                                    <a href="https://twitter.com/intent/tweet?url={{ urlencode($video->getShareUrl()) }}&text={{ urlencode($video->title) }}" 
                                       target="_blank"
                                       class="flex items-center justify-center w-10 h-10 bg-blue-400 text-white rounded-full hover:bg-blue-500">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                        </svg>
                                    </a>
                                    
                                    <!-- WhatsApp Share -->
                                    <a href="https://wa.me/?text={{ urlencode($video->title . ' ' . $video->getShareUrl()) }}" 
                                       target="_blank"
                                       class="flex items-center justify-center w-10 h-10 bg-green-500 text-white rounded-full hover:bg-green-600">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @endif
                    @else
                        <!-- For non-owners or unauthenticated users -->
                        <div class="bg-gray-50 border border-gray-200 rounded-md p-4">
                            <p class="text-sm text-gray-600">
                                Only the video owner can generate and manage share links.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    function toggleCommentButton(textarea) {
        const submitBtn = document.getElementById('commentSubmitBtn');
        if (submitBtn) {
            submitBtn.disabled = !textarea.value.trim();
        }
    }

    function toggleReplyButton(textarea, commentId) {
        const submitBtn = document.getElementById('replySubmitBtn' + commentId);
        if (submitBtn) {
            submitBtn.disabled = !textarea.value.trim();
        }
    }

    function copyToClipboard() {
        const shareUrl = document.getElementById('shareUrl');
        shareUrl.select();
        shareUrl.setSelectionRange(0, 99999); // For mobile devices
        
        try {
            document.execCommand('copy');
            // Show temporary feedback
            const button = event.target;
            const originalText = button.textContent;
            button.textContent = 'Copied!';
            button.classList.add('bg-green-600');
            button.classList.remove('bg-blue-600');
            
            setTimeout(() => {
                button.textContent = originalText;
                button.classList.remove('bg-green-600');
                button.classList.add('bg-blue-600');
            }, 2000);
        } catch (err) {
            console.error('Failed to copy: ', err);
        }
    }

    // Reset comment form after successful submission
    document.addEventListener('livewire:dispatched', function (event) {
        if (event.detail.name === 'comment-posted') {
            const commentTextarea = document.querySelector('textarea[placeholder="Add a comment..."]');
            const submitBtn = document.getElementById('commentSubmitBtn');
            if (commentTextarea && submitBtn) {
                commentTextarea.value = '';
                submitBtn.disabled = true;
            }
        }
    });

    // Re-enable button state management after Livewire updates
    document.addEventListener('livewire:morph-updated', function () {
        // Re-check comment button state
        const commentTextarea = document.querySelector('textarea[placeholder="Add a comment..."]');
        if (commentTextarea) {
            toggleCommentButton(commentTextarea);
        }
        
        // Re-check reply button states
        document.querySelectorAll('textarea[placeholder="Add a reply..."]').forEach(function(textarea) {
            const form = textarea.closest('form');
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = !textarea.value.trim();
            }
        });
    });
</script>
