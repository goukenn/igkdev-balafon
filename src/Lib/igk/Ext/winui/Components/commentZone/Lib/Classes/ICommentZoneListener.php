<?php

namespace IGK\Ext\WinUI\Components\CommentZone;


/**
 * 
 * @package IGK\Ext\WinUI\Components\CommentZone
 */
interface ICommentZoneListener {
	function comment_add_ajx($i);
	function comment_drop_ajx($i);
	function comment_viewmore_ajx($id);
} 
