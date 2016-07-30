<?php
/**
 * This file is part of LaraBB.
 *
 * (c) Jason Clemons <jason@larabb.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * that was distributed with this source code.
 */

namespace Christhompsontldr\Laraboard\Http\Controllers;

use App\Http\Controllers\Controller;
use Gate;
use Illuminate\Http\Request;

use Christhompsontldr\Laraboard\Models\Board;
use Christhompsontldr\Laraboard\Models\Category;
use Christhompsontldr\Laraboard\Models\Post;
use Christhompsontldr\Laraboard\Models\Thread;

class BoardController extends Controller
{
	/**
	 *
	 */
    public function show($category_slug, $slug)
    {
    	$board = Board::whereSlug($slug)->firstOrFail();

        //  @todo figure why can't paginate on getDescendants();
        $threads = Post::where('id', $board->id)->first()->getDescendants();//->paginate(config('laraboard.board.limit', 2));

        $threads = Thread::whereIn('id', $threads->pluck('id'))->paginate(config('laraboard.board.limit', 15));

    	return view('laraboard::board.show', compact('board', 'threads'));
    }

    public function create($parent_slug = null)
    {
        /**
        * @todo Limit this list to the categories this user can manage
        */
        $categories = Category::get()->lists('name','id');

        $category = Category::whereSlug($parent_slug)->first();

        $this->authorize('laraboard::board-create', $category);

        $parent_id = null;
        if (!empty($category)) {
            $parent_id = $category->id;
        }

        return view('laraboard::board.create', compact('categories','parent_id'));
    }

    public function store(Request $request)
    {
        $category = Category::findOrFail($request->parent_id);

        $this->authorize('laraboard::board-create', $category);

        $this->validate($request, [
            'name' => 'required|max:255',
            'body' => 'max:255'
        ]);

        $board          = new Post;
        $board->name    = $request->name;
        $board->body    = $request->body;
        $board->type    = 'Board';
        $board->user_id = \Auth::user()->id;
        $board->save();
        $board->makeChildOf($category);

        return redirect()->route('board.show', [$board->slug, $board->name_slug])->with('success', 'Board created successfully.');
    }

    public function edit($slug)
    {
        $board = Board::whereSlug($slug)->firstOrFail();

        $this->authorize('laraboard::board-edit', $board);

        return view('laraboard::board.edit', compact('board'));
    }

    public function update(Request $request)
    {
        $board = Board::findOrFail($request->id);

        $this->authorize('laraboard::board-edit', $board);

        $this->validate($request, [
            'name' => 'required|max:255',
            'body' => 'max:255'
        ]);

        $board->name    = $request->name;
        $board->body    = $request->body;
        $board->user_id = \Auth::user()->id;
        $board->save();

        return redirect()->route('board.show', [$board->slug, $board->name_slug])->with('success', 'Board updated successfully.');
    }
}
