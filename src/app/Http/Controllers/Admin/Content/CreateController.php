<?php

namespace Kaleidoscope\Factotum\Http\Controllers\Admin\Content;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

use Kaleidoscope\Factotum\Content;
use Kaleidoscope\Factotum\ContentType;

class CreateController extends Controller
{

	public function create( $contentTypeId )
	{
		$this->_prepareContentFields( $contentTypeId );

		$contentType = ContentType::find($contentTypeId);

		return view('factotum::admin.content.edit')
					->with('title', Lang::get('factotum::content.add_new') . ' ' . $contentType->content_type)
					->with('postUrl', url('/admin/content/store/' . $contentTypeId) )
					->with('contentType', $contentType)
					->with('statuses', $this->statuses)
					->with('contentFields', $this->_contentFields)
					->with('contentsTree', $this->_contents)
					->with('categoriesTree', $this->_categories);
	}

	public function store( $contentTypeId, Request $request )
	{
		$data = $request->all();
		$data['content_type_id'] = $contentTypeId;
		$this->validator( $request, $data )->validate();

		$content = new Content;
		$content->content_type_id = $contentTypeId;

		$this->_save( $request, $content );

		return redirect( 'admin/content/list/' . $contentTypeId )
						->with('message', Lang::get('factotum::content.success_create_content'));
	}

}

