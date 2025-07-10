<x-layouts.youtube-app>
    <x-slot name="title">{{ $video->title }} - OpenMediaPlatform</x-slot>
    
    <div class="flex gap-6 p-6">
        <!-- Main Content -->
        <div class="flex-1">
            <!-- Video Player -->
            <div class="aspect-video bg-black rounded-lg overflow-hidden mb-4">
                <livewire:video-player :video-id="$video->vid" />
            </div>

            <!-- Video Info -->
            <div class="mb-6">
                <h1 class="text-xl font-semibold mb-2">{{ $video->title }}</h1>
                
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-4">
                        <!-- Channel Info -->
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                                {{ substr($video->user ? $video->user->name : 'U', 0, 1) }}
                            </div>
                            <div>
                                <h3 class="font-semibold">{{ $video->user ? $video->user->name : 'Unknown' }}</h3>
                                <p class="text-sm text-gray-600">{{ $video->user ? number_format($video->user->subscribers_count ?? 0) : '0' }} subscribers</p>
                            </div>
                        </div>
                        
                        <!-- Subscribe Button -->
                        @auth
                            @if($video->user_id !== auth()->id())
                                <button class="px-4 py-2 bg-red-600 text-white rounded-full hover:bg-red-700 transition-colors">
                                    Subscribe
                                </button>
                            @endif
                        @else
                            <a href="#" class="px-4 py-2 bg-red-600 text-white rounded-full hover:bg-red-700 transition-colors">
                                Subscribe
                            </a>
                        @endauth
                    </div>

                    <!-- Video Actions -->
                    <div class="flex items-center space-x-2">
                        <!-- Like/Dislike -->
                        <div class="flex items-center bg-gray-100 rounded-full">
                            @auth
                                <button wire:click="toggleLike" 
                                        class="flex items-center space-x-1 px-4 py-2 hover:bg-gray-200 rounded-l-full
                                               {{ $userEngagement && $userEngagement->engagement_type === 'like' ? 'text-blue-600' : '' }}">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M7.493 18.75c-.425 0-.82-.236-.975-.632A7.48 7.48 0 016 15.125c0-1.75.599-3.358 1.602-4.634.151-.192.373-.309.6-.397.473-.183.89-.514 1.212-.924a9.042 9.042 0 012.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 00.322-1.672V3a.75.75 0 01.75-.75 2.25 2.25 0 012.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558-.641 1.05-1.07 1.467-.329.32-.717.564-1.148.675a3.036 3.036 0 01-1.092.052L9 9.75v7.875c0 .414.336.75.75.75h6.102c.727-.002 1.36-.634 1.41-1.368l.885-13.283c.047-.71-.398-1.392-1.11-1.392H4.25a.75.75 0 00-.75.75v15c0 .414.336.75.75.75H7.493z"/>
                                    </svg>
                                    <span>{{ number_format($video->likes) }}</span>
                                </button>
                                <div class="w-px h-6 bg-gray-300"></div>
                                <button wire:click="toggleDislike" 
                                        class="flex items-center space-x-1 px-4 py-2 hover:bg-gray-200 rounded-r-full
                                               {{ $userEngagement && $userEngagement->engagement_type === 'dislike' ? 'text-red-600' : '' }}">
                                    <svg class="w-5 h-5 rotate-180" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M7.493 18.75c-.425 0-.82-.236-.975-.632A7.48 7.48 0 016 15.125c0-1.75.599-3.358 1.602-4.634.151-.192.373-.309.6-.397.473-.183.89-.514 1.212-.924a9.042 9.042 0 012.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 00.322-1.672V3a.75.75 0 01.75-.75 2.25 2.25 0 012.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558-.641 1.05-1.07 1.467-.329.32-.717.564-1.148.675a3.036 3.036 0 01-1.092.052L9 9.75v7.875c0 .414.336.75.75.75h6.102c.727-.002 1.36-.634 1.41-1.368l.885-13.283c.047-.71-.398-1.392-1.11-1.392H4.25a.75.75 0 00-.75.75v15c0 .414.336.75.75.75H7.493z"/>
                                    </svg>
                                </button>
                            @else
                                <div class="flex items-center space-x-1 px-4 py-2">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M7.493 18.75c-.425 0-.82-.236-.975-.632A7.48 7.48 0 016 15.125c0-1.75.599-3.358 1.602-4.634.151-.192.373-.309.6-.397.473-.183.89-.514 1.212-.924a9.042 9.042 0 012.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 00.322-1.672V3a.75.75 0 01.75-.75 2.25 2.25 0 012.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558-.641 1.05-1.07 1.467-.329.32-.717.564-1.148.675a3.036 3.036 0 01-1.092.052L9 9.75v7.875c0 .414.336.75.75.75h6.102c.727-.002 1.36-.634 1.41-1.368l.885-13.283c.047-.71-.398-1.392-1.11-1.392H4.25a.75.75 0 00-.75.75v15c0 .414.336.75.75.75H7.493z"/>
                                    </svg>
                                    <span>{{ number_format($video->likes) }}</span>
                                </div>
                            @endauth
                        </div>

                        <!-- Share Button -->
                        <button class="flex items-center space-x-1 px-4 py-2 bg-gray-100 rounded-full hover:bg-gray-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                            </svg>
                            <span>Share</span>
                        </button>
                    </div>
                </div>

                <!-- Video Stats -->
                <div class="flex items-center space-x-4 text-sm text-gray-600 mb-4">
                    <span>{{ number_format($video->views) }} views</span>
                    <span>•</span>
                    <span>{{ $video->created_at->format('M j, Y') }}</span>
                </div>

                <!-- Description -->
                @if($video->description)
                    <div class="bg-gray-100 rounded-lg p-4">
                        <p class="whitespace-pre-wrap">{{ $video->description }}</p>
                    </div>
                @endif
            </div>

            <!-- Comments Section -->
            <div>
                <h3 class="text-lg font-semibold mb-4">{{ number_format($video->comments) }} Comments</h3>
                
                <!-- Add Comment -->
                @auth
                    <div class="mb-6">
                        <div class="flex space-x-3">
                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <div class="flex-1">
                                <form wire:submit="addComment">
                                    <textarea wire:model="newComment" 
                                              placeholder="Add a comment..." 
                                              class="w-full p-3 border-b-2 border-gray-200 focus:border-blue-500 focus:outline-none resize-none"
                                              rows="1"></textarea>
                                    <div class="flex justify-end space-x-2 mt-2">
                                        <button type="button" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">
                                            Cancel
                                        </button>
                                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                            Comment
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <p class="text-gray-600 mb-6">
                        <a href="#" class="text-blue-600 hover:underline">Sign in</a> to leave a comment.
                    </p>
                @endauth

                <!-- Comments List -->
                <div class="space-y-4">
                    @foreach($comments as $comment)
                        <div class="flex space-x-3">
                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                {{ substr($comment->user->name, 0, 1) }}
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-1">
                                    <span class="font-semibold text-sm">{{ $comment->user->name }}</span>
                                    <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm">{{ $comment->content }}</p>
                                
                                <!-- Comment Actions -->
                                <div class="flex items-center space-x-4 mt-2">
                                    <button class="flex items-center space-x-1 text-xs text-gray-600 hover:text-gray-800">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M7.493 18.75c-.425 0-.82-.236-.975-.632A7.48 7.48 0 016 15.125c0-1.75.599-3.358 1.602-4.634.151-.192.373-.309.6-.397.473-.183.89-.514 1.212-.924a9.042 9.042 0 012.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 00.322-1.672V3a.75.75 0 01.75-.75 2.25 2.25 0 012.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558-.641 1.05-1.07 1.467-.329.32-.717.564-1.148.675a3.036 3.036 0 01-1.092.052L9 9.75v7.875c0 .414.336.75.75.75h6.102c.727-.002 1.36-.634 1.41-1.368l.885-13.283c.047-.71-.398-1.392-1.11-1.392H4.25a.75.75 0 00-.75.75v15c0 .414.336.75.75.75H7.493z"/>
                                        </svg>
                                        <span>{{ $comment->likes }}</span>
                                    </button>
                                    
                                    <button class="flex items-center space-x-1 text-xs text-gray-600 hover:text-gray-800">
                                        <svg class="w-4 h-4 rotate-180" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M7.493 18.75c-.425 0-.82-.236-.975-.632A7.48 7.48 0 016 15.125c0-1.75.599-3.358 1.602-4.634.151-.192.373-.309.6-.397.473-.183.89-.514 1.212-.924a9.042 9.042 0 012.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 00.322-1.672V3a.75.75 0 01.75-.75 2.25 2.25 0 012.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558-.641 1.05-1.07 1.467-.329.32-.717.564-1.148.675a3.036 3.036 0 01-1.092.052L9 9.75v7.875c0 .414.336.75.75.75h6.102c.727-.002 1.36-.634 1.41-1.368l.885-13.283c.047-.71-.398-1.392-1.11-1.392H4.25a.75.75 0 00-.75.75v15c0 .414.336.75.75.75H7.493z"/>
                                        </svg>
                                    </button>
                                    
                                    @auth
                                        <button wire:click="setReplyTo({{ $comment->id }})" class="text-xs text-gray-600 hover:text-gray-800">
                                            Reply
                                        </button>
                                    @endauth
                                </div>

                                <!-- Reply Form -->
                                @if($replyTo === $comment->id)
                                    <div class="mt-3">
                                        <form wire:submit="addReply">
                                            <textarea wire:model="replyContent" 
                                                      placeholder="Add a reply..." 
                                                      class="w-full p-3 border border-gray-200 rounded focus:border-blue-500 focus:outline-none resize-none"
                                                      rows="2"></textarea>
                                            <div class="flex justify-end space-x-2 mt-2">
                                                <button type="button" wire:click="cancelReply" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">
                                                    Cancel
                                                </button>
                                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                                    Reply
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                <!-- Replies -->
                                @if($comment->replies->count() > 0)
                                    <div class="mt-4 space-y-3">
                                        @foreach($comment->replies as $reply)
                                            <div class="flex space-x-3">
                                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white text-xs font-semibold">
                                                    {{ substr($reply->user->name, 0, 1) }}
                                                </div>
                                                <div class="flex-1">
                                                    <div class="flex items-center space-x-2 mb-1">
                                                        <span class="font-semibold text-sm">{{ $reply->user->name }}</span>
                                                        <span class="text-xs text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                                                    </div>
                                                    <p class="text-sm">{{ $reply->content }}</p>
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

        <!-- Sidebar - Related Videos -->
        <div class="w-96">
            <h3 class="text-lg font-semibold mb-4">Up next</h3>
            <div class="space-y-3">
                @foreach($relatedVideos as $relatedVideo)
                    <a href="{{ route('videos.show', $relatedVideo) }}" wire:navigate class="flex space-x-3 hover:bg-gray-50 p-2 rounded">
                        <div class="flex-shrink-0">
                            <div class="w-40 aspect-video bg-gray-200 rounded overflow-hidden">
                                <img src="{{ $relatedVideo->getThumbnailUrl() }}" 
                                     alt="{{ $relatedVideo->title }}" 
                                     class="w-full h-full object-cover">
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-sm line-clamp-2 mb-1">{{ $relatedVideo->title }}</h4>
                            <p class="text-xs text-gray-600">{{ $relatedVideo->user ? $relatedVideo->user->name : 'Unknown' }}</p>
                            <div class="flex items-center space-x-1 text-xs text-gray-500 mt-1">
                                <span>{{ number_format($relatedVideo->views) }} views</span>
                                <span>•</span>
                                <span>{{ $relatedVideo->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.youtube-app>
