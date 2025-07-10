<div class="max-w-7xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Video Section -->
        <div class="lg:col-span-2">
            <!-- Video Player -->
            <div class="bg-black rounded-lg overflow-hidden aspect-video">
                <video id="videoPlayer" 
                       controls 
                       class="w-full h-full"
                       onloadedmetadata="updateWatchTime()"
                       ontimeupdate="updateWatchTime()">
                    <source src="{{ route('videos.raw', $video) }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
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
                        
                        <button class="flex items-center space-x-2 px-4 py-2 bg-gray-100 rounded-full hover:bg-gray-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                            </svg>
                            <span>Share</span>
                        </button>
                    </div>
                </div>

                <!-- Channel Information -->
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white text-lg font-semibold">
                            {{ substr($video->user->getChannelName(), 0, 1) }}
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $video->user->getChannelName() }}</h3>
                            <p class="text-sm text-gray-600">{{ number_format($video->user->subscribers_count) }} subscribers</p>
                        </div>
                    </div>
                    
                    @auth
                        @if($video->user_id !== auth()->id())
                            <button class="px-6 py-2 bg-red-600 text-white rounded-full hover:bg-red-700 transition-colors">
                                Subscribe
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
                        <form wire:submit.prevent="addComment" class="mb-6">
                            <div class="flex space-x-3">
                                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                                <div class="flex-1">
                                    <textarea wire:model="newComment" 
                                              placeholder="Add a comment..."
                                              class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 resize-none"
                                              rows="3"></textarea>
                                    <div class="flex justify-end space-x-2 mt-2">
                                        <button type="button" 
                                                wire:click="$set('newComment', '')"
                                                class="px-4 py-2 text-gray-600 hover:text-gray-800">
                                            Cancel
                                        </button>
                                        <button type="submit" 
                                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50"
                                                {{ empty($newComment) ? 'disabled' : '' }}>
                                            Comment
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
                                <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                    {{ substr($comment->user->name, 0, 1) }}
                                </div>
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
                                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-semibold">
                                                    {{ substr(auth()->user()->name, 0, 1) }}
                                                </div>
                                                <div class="flex-1">
                                                    <textarea wire:model="replyContent" 
                                                              placeholder="Add a reply..."
                                                              class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 resize-none"
                                                              rows="2"></textarea>
                                                    <div class="flex justify-end space-x-2 mt-2">
                                                        <button type="button" 
                                                                wire:click="cancelReply"
                                                                class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800">
                                                            Cancel
                                                        </button>
                                                        <button type="submit" 
                                                                class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                                                            Reply
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
                                                    <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center text-white text-xs font-semibold">
                                                        {{ substr($reply->user->name, 0, 1) }}
                                                    </div>
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

        <!-- Sidebar - Related Videos -->
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
    </div>
</div>

<script>
    function updateWatchTime() {
        const video = document.getElementById('videoPlayer');
        if (video && video.currentTime > 0) {
            // Send watch time update to backend periodically
            // This can be improved with Livewire wire:poll or custom events
        }
    }
</script>
