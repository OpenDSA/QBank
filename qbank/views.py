# Create your views here.

from django.shortcuts import render_to_response, get_object_or_404
from django.core.context_processors import csrf
from django.utils.encoding import smart_str
from django.template import RequestContext, loader,Context
from django.forms.formsets import formset_factory, BaseFormSet
from django.core.management.base import BaseCommand, CommandError
from django.forms.models import inlineformset_factory, BaseInlineFormSet
from django.http import HttpResponse, HttpResponseRedirect
from django.contrib.auth.decorators import login_required
from qbank.forms import *
from qbank.models import *

import mimetypes
from django.core.servers.basehttp import FileWrapper
import csv
import os


#@login_required
def index(request):
    # This class is used to make empty formset forms required
	class RequiredFormSet(BaseFormSet):
		def __init__(self, *args, **kwargs):
			super(RequiredFormSet, self).__init__(*args, **kwargs)
			for form in self.forms:
				form.empty_permitted = True
	HintFormSet = formset_factory(HintForm, max_num = 10, formset = RequiredFormSet)
	VariableFormSet = formset_factory(VariableForm, max_num = 10, formset = RequiredFormSet)
	ChoiceFormSet = formset_factory(ChoiceForm, max_num = 10, formset = RequiredFormSet)
	ScriptFormSet = formset_factory(ScriptForm, max_num = 10, formset = RequiredFormSet)


	if request.method == 'POST': # If the form has been submitted...
		problem_form = ProblemForm(request.POST)
		problem_template_form = ProblemTemplateForm(request.POST, prefix='template')
		answer_form = AnswerForm(request.POST, prefix='answer')

		hint_formset = HintFormSet(request.POST, request.FILES, prefix='hints')
		variable_formset = VariableFormSet(request.POST, request.FILES, prefix='variables')
		choice_formset = ChoiceFormSet(request.POST, request.FILES, prefix='choices')
		script_formset = ScriptFormSet(request.POST, request.FILES, prefix='scripts')
		if problem_form.is_valid() and problem_template_form.is_valid() and choice_formset.is_valid() and hint_formset.is_valid() and variable_formset.is_valid() and answer_form.is_valid() and script_formset.is_valid():
			problem = problem_form.save()
			problem_template = problem_template_form.save(commit = False)
			problem_template.problem = problem
			problem_template.save()

			answer = answer_form.save(commit = False)
			answer.problem = problem
			answer.save()
			for form in hint_formset.forms:
				hint = form.save(commit = False)
				hint.problem = problem
				hint.save()
			for form in variable_formset.forms:
				variable = form.save(commit = False)
				variable.problem = problem
				variable.save()
			for form in script_formset.forms:
				script = form.save(commit = False)
				script.problem = problem
				script.save()
			for form in choice_formset.forms:
				choice = form.save(commit = False)
				choice.problem = problem
				choice.save() # Redirect to a 'success' page
			return HttpResponseRedirect('/qbank/problems/')
	else:	
		problem_form = ProblemForm()
		choice_formset = ChoiceFormSet(prefix='choices')
		problem_template_form = ProblemTemplateForm(prefix='template')
		answer_form = AnswerForm(prefix='answer')
		script_formset = ScriptFormSet(prefix='scripts')
		variable_formset = VariableFormSet(prefix='variables')
		hint_formset = HintFormSet(prefix='hints')

	c = {'problem_form' : problem_form,
	     'choice_formset' : choice_formset,
	     'problem_template_form' : problem_template_form,
	     'answer_form': answer_form,
	     'variable_formset' : variable_formset,
	     'script_formset' : script_formset,
	     'hint_formset' : hint_formset,
	}
	c.update(csrf(request))
	return render_to_response('add.html', c)


#@login_required
def edit(request, problem_id):

	problem = get_object_or_404(Problem, id=problem_id)

	class RequiredFormSet(BaseFormSet):
		def __init__(self, *args, **kwargs):
			super(RequiredFormSet, self).__init__(*args, **kwargs)
			for form in self.forms:
				form.empty_permitted = True
	class MyInline(BaseInlineFormSet): 
   		def __init__(self, *args, **kwargs): 
			super(MyInline, self).__init__(*args, **kwargs) 
			self.can_delete = False 

	
	
	maxpt = max(0, len(ProblemTemplate.objects.filter(problem=problem)))
	ProblemTemplateInlineFormSet = inlineformset_factory(Problem, ProblemTemplate, max_num=maxpt)
	maxa = max(0, len(Answer.objects.filter(problem=problem)))

	AnswerInlineFormSet = inlineformset_factory(Problem, Answer, max_num =maxa)
	maxv = max(0, len(Variable.objects.filter(problem=problem)))

	VariableInlineFormSet = inlineformset_factory(Problem, Variable, max_num=maxv)
	maxc = max(0, len(CommonIntroduction.objects.filter(problem=problem)))

	CommonIntroductionFormSet =  inlineformset_factory(Problem, CommonIntroduction, max_num =maxc )
	maxch = max(0, len(Choice.objects.filter(problem=problem)))

	ChoiceInlineFormSet = inlineformset_factory(Problem, Choice, max_num=maxch, formset=MyInline)
	maxh = max(0, len(Hint.objects.filter(problem=problem)))

	HintInlineFormSet = inlineformset_factory(Problem, Hint, max_num=maxh)
	maxs = max(0, len(Script.objects.filter(problem=problem)))

	ScriptInlineFormSet = inlineformset_factory(Problem, Script, max_num=maxs)

	if request.method == 'POST':
		problem_form =ProblemForm(request.POST, instance=problem)
		problem_template_formset = ProblemTemplateInlineFormSet(request.POST,request.FILES, instance=problem, prefix='templates')
		common_introduction_formset = CommonIntroductionForm(request.POST, request.FILES, prefix='common_intro', instance =problem)

		answer_formset = AnswerInlineFormSet(request.POST, instance=problem, prefix='answer')

		hint_formset = HintInlineFormSet(request.POST, request.FILES, instance=problem,  prefix='hints')
		choice_formset = ChoiceInlineFormSet(request.POST, request.FILES,  instance=problem, prefix='choices')
		script_formset = ScriptInlineFormSet(request.POST, request.FILES,  instance=problem,  prefix='scripts')
		variable_formset = VariableInlineFormSet(request.POST, request.FILES,instance=problem, prefix='variables')
		
		if problem_form.is_valid() and variable_formset.is_valid() and problem_template_formset.is_valid() and choice_formset.is_valid() and hint_formset.is_valid() and answer_formset.is_valid() and script_formset.is_valid() and common_introduction_formset.is_valid() :
			problem = problem_form.save()
			answer_formset.save(commit = False)
			common_introduction_formset.save(commit = False)
			problem_template_formset.save(commit =False)
			variable_formset.save(commit = False)


			for form in hint_formset.forms:
				hint = form.save(commit = False)
				hint.problem = problem
				hint.save()


			for form in script_formset.forms:
				script = form.save(commit = False)
				script.problem = problem
				script.save()


			for form in choice_formset.forms:
				choice = form.save(commit = False)
				choice.problem = problem
				choice.save()
			return HttpResponseRedirect('/qbank/problems/')
	else:
		problem_form = ProblemForm(instance=problem)
		
		problem_template_formset = ProblemTemplateInlineFormSet( instance=problem, prefix='templates')

		choice_formset = ChoiceInlineFormSet(instance=problem, prefix='choices')
		
		answer_formset = AnswerInlineFormSet(instance=problem, prefix='answer')
		
		script_formset = ScriptInlineFormSet(instance=problem, prefix='scripts')
		variable_formset = VariableInlineFormSet(instance=problem, prefix='variables')
		common_introduction_formset = CommonIntroductionFormSet(instance=problem, prefix='common_intro')
		hint_formset = HintInlineFormSet( instance=problem, prefix='hints')

		
	c = {
	'problem_form' : problem_form,
	'choice_formset' : choice_formset,
	'problem_template_formset' :problem_template_formset,
	'answer_formset': answer_formset,
	'variable_formset' : variable_formset,
	'script_formset' : script_formset,
	'common_introduction_formset' : common_introduction_formset,
	'hint_formset' : hint_formset,	
    	}
	c.update(csrf(request))
	return render_to_response('edit.html', c)

def splashpage(request):
	return HttpResponseRedirect('splashpage')



def problems(request):
	problems = Problem.objects.all()
	context = Context({'problems':problems})
	return render_to_response('problems.html', context, context_instance=RequestContext(request))


def ka_error(request, problem_id):
	problems = Problem.objects.all()
	p = get_object_or_404(Problem, id=problem_id)
	context = Context({'p':p})
	return render_to_response('ka_error.html', context)

def export(request):
	problems = Problem.objects.all()
	context = Context({'problems':problems})
	return render_to_response('export.html', context)


def problems_Summary(request):
	problems = Problem.objects.all()
	context = Context({'problems':problems})
	return render_to_response('problems_Summary.html', context)

def ka_details(request, problem_id):
	p = get_object_or_404(Problem, id=problem_id)
	q = ProblemTemplate.objects.get(problem = p)
#	v = Variable.objects.get(problem = p)
	s = Answer.objects.get(problem = p)
#	c = Choice.objects.get(problem = p)
	h = p.hint_set.all()
	
	destination = open('/home/OpenDSA/exercises/'+p.title+'.html', 'wb+')
	str ="<!DOCTYPE html>"+"\n"+"<html data-require=\"math math-format word-problems spin\"><head>"+"\n"+"<title>"+"\n"+p.title+"\n"+"</title>"+"\n"+"<script src=\"../../lib/jquery.min.js\">"+"\n"+"</script>"+"\n"+"<script src=\"../../lib/jquery-ui.min.js\">"+"\n"+"</script>"+"\n"+"<script>urlBaseOverride = \"../../ODSAkhan-exercises/\";</script>"+"\n"+"<script src=\"../../lib/khan-exercise-min.js\">"+"\n"+"</script>"+"\n"+"<script type=\"text/javascript\"src=\"http://cdn.mathjax.org/mathjax/1.1-latest/MathJax.js?config=http://algoviz.org/OpenDSA/ODSAkhan-exercises/KAthJax-77111459c7d82564a705f9c5480e2c88.js\">"+"\n"+"</script>"+"\n"
	for scr in p.script_set.all():
		str += "<p>"
		str += scr.script
		str += "</p>"
		str += "\n"



	str += "</head>"+"\n"+"<body>"+"\n"+"<div class=\"exercise\">"+"\n"+"<div class=\"vars\">"+"\n"

	for t in p.variable_set.all():
		str +="<var id=\""
		str += t.var_id
		str += "\">"	
		str += t.var_value
		str += "</var>"
		str += "\n"


	str += "</div>"+"\n"+" <div class=\"problems\">"+"\n"
	if "spin" in q.question:
		str += "<div id =\"problem\">"
	str += q.question
	str += "</div>"+"\n"
	str += "<div class=\"solution\""
	if "spin" not in q.question:
		str += "data-type=\"custom\""
	str += ">"+"\n"
	str += s.solution
	str += "</div>"
	str += "\n"

	
	for c in p.choice_set.all():

		if c.choice == "":
			break
		else:
			str += "<ul class =\"choices\">"
			break

	
	for c in p.choice_set.all():

		if not c.choice == "":
			str += "<li><var>"
			str += c.choice
			str += "</var></li>"
			str += "\n"

	for c in p.choice_set.all():

		if c.choice == "":
			break
		else:
			str += "</ul>"
			break


	str += "<div class=\"hints\">"
	str += "\n"
	for h in p.hint_set.all():
		str += "<p>"
		str += h.hint
		str += "</p>"
		str += "\n"
	str += "</div>"+"\n"
	if "spin" in q.question:
		str += "</div>"
	str += "</div>"+"\n"+"</div>"+"\n"+"</body>"+"\n"+"</html>"+"\n"	
	destination.write(bytes(str))
	destination.close()


	context = Context({
		'p':p,
		'title':p.title,	
		'question':q,
		'solution':s,
	#	'choice':c,
#		'hint':h
	})
	return render_to_response('ka_details.html', context)



#@login_required
def simple_details(request, problem_id):
	p = get_object_or_404(Problem, id=problem_id)
	q = ProblemTemplate.objects.get(problem = p)
#	v = Variable.objects.get(problem = p)
	s = Answer.objects.get(problem = p)
#	c = Choice.objects.get(problem = p)
	h = p.hint_set.all()
	destination = open("/home/OpenDSA/exercises/"+p.title+".html", 'wb+')
	
	str ="<!DOCTYPE html><html data-require=\"math math-format word-problems spin\"><head>"+"\n"+"<title>"+"\n"+p.title+"</title>"+"\n"+"<script src=\"../../lib/jquery.min.js\">"+"\n"+"</script>"+"\n"+"<script src=\"../../lib/jquery-ui.min.js\">"+"\n"+"</script>"+"\n"+"<script>urlBaseOverride = \"../../ODSAkhan-exercises/\";</script>"+"\n"+"<script src=\"../../lib/khan-exercise-min.js\">"+"\n"+"</script>"+"\n"+"</head><body><div class=\"exercise\"><div class=\"vars\"></div><div class=\"problems\"><div id=\"problem-type-or-description\"><p class=\"question\">"
	str += q.question
	str += "</p>"
	str += "<div class=\"solution\"><var>\""
	str += s.solution
	str += "\"</var></div>"
	str += "<ul class =\"choices\">"
	for c in p.choice_set.all():
		str += "<li><var>\""
		str += c.choice
		str += "\"</var></li>"
	str += "</ul>"
	str += "<div class=\"hints\">"
	for h in p.hint_set.all():
		str += "<p>\""
		str += h.hint
		str += "\"</p>"
	str += "</div>"
	str += "</div></div></div></body>"+"\n"+"<script type=\"text/javascript\"src=\"http://cdn.mathjax.org/mathjax/1.1-latest/MathJax.js?config=http://algoviz.org/OpenDSA/ODSAkhan-exercises/KAthJax-77111459c7d82564a705f9c5480e2c88.js\">"+"\n"+"</script>"+"</html>"	

	destination.write(bytes(str))
	destination.close()

	context = Context({
		'p':p,
		'title':p.title,	
		'question':q,
		'solution':s,
	#	'choice':c,
#		'hint':h
	})
	return render_to_response('simple_details.html', context)


def edit_ka(request, problem_id):

	problem = get_object_or_404(Problem, id=problem_id)

	class RequiredFormSet(BaseFormSet):
		def __init__(self, *args, **kwargs):
			super(RequiredFormSet, self).__init__(*args, **kwargs)
			for form in self.forms:
				form.empty_permitted = True
	class MyInline(BaseInlineFormSet): 
   		def __init__(self, *args, **kwargs): 
			super(MyInline, self).__init__(*args, **kwargs) 
			self.can_delete = False 

	
	
	maxpt = max(0, len(ProblemTemplate.objects.filter(problem=problem)))
	ProblemTemplateInlineFormSet = inlineformset_factory(Problem, ProblemTemplate, max_num=maxpt)
	maxa = max(0, len(Answer.objects.filter(problem=problem)))

	AnswerInlineFormSet = inlineformset_factory(Problem, Answer, max_num =maxa)
	maxv = max(0, len(Variable.objects.filter(problem=problem)))

	VariableInlineFormSet = inlineformset_factory(Problem, Variable, max_num=maxv)
	maxc = max(0, len(CommonIntroduction.objects.filter(problem=problem)))

	CommonIntroductionFormSet =  inlineformset_factory(Problem, CommonIntroduction, max_num =maxc )
	maxch = max(0, len(Choice.objects.filter(problem=problem)))

	ChoiceInlineFormSet = inlineformset_factory(Problem, Choice, max_num=maxch, formset=MyInline)
	maxh = max(0, len(Hint.objects.filter(problem=problem)))

	HintInlineFormSet = inlineformset_factory(Problem, Hint, max_num=maxh)
	maxs = max(0, len(Script.objects.filter(problem=problem)))

	ScriptInlineFormSet = inlineformset_factory(Problem, Script, max_num=maxs)

	if request.method == 'POST':
		problem_form =ProblemForm(request.POST, instance=problem)
		problem_template_formset = ProblemTemplateInlineFormSet(request.POST,request.FILES, instance=problem, prefix='templates')
		common_introduction_formset = CommonIntroductionForm(request.POST, request.FILES, prefix='common_intro', instance =problem)

		answer_formset = AnswerInlineFormSet(request.POST, instance=problem, prefix='answer')

		hint_formset = HintInlineFormSet(request.POST, request.FILES, instance=problem,  prefix='hints')
		choice_formset = ChoiceInlineFormSet(request.POST, request.FILES,  instance=problem, prefix='choices')
		script_formset = ScriptInlineFormSet(request.POST, request.FILES,  instance=problem,  prefix='scripts')
		variable_formset = VariableInlineFormSet(request.POST, request.FILES,instance=problem, prefix='variables')
		
		if problem_form.is_valid() and variable_formset.is_valid() and problem_template_formset.is_valid() and choice_formset.is_valid() and hint_formset.is_valid() and answer_formset.is_valid() and script_formset.is_valid() and common_introduction_formset.is_valid() :
			problem = problem_form.save()
			answer_formset.save(commit = False)
			common_introduction_formset.save(commit = False)
			problem_template_formset.save(commit =False)
			variable_formset.save(commit = False)


			for form in hint_formset.forms:
				hint = form.save(commit = False)
				hint.problem = problem
				hint.save()


			for form in script_formset.forms:
				script = form.save(commit = False)
				script.problem = problem
				script.save()


			for form in choice_formset.forms:
				choice = form.save(commit = False)
				choice.problem = problem
				choice.save()
			return HttpResponseRedirect('/qbank/problems/')
	else:
		problem_form = ProblemForm(instance=problem)
		
		problem_template_formset = ProblemTemplateInlineFormSet( instance=problem, prefix='templates')

		choice_formset = ChoiceInlineFormSet(instance=problem, prefix='choices')
		
		answer_formset = AnswerInlineFormSet(instance=problem, prefix='answer')
		
		script_formset = ScriptInlineFormSet(instance=problem, prefix='scripts')
		variable_formset = VariableInlineFormSet(instance=problem, prefix='variables')
		common_introduction_formset = CommonIntroductionFormSet(instance=problem, prefix='common_intro')
		hint_formset = HintInlineFormSet( instance=problem, prefix='hints')

		
	c = {
	'problem_form' : problem_form,
	'choice_formset' : choice_formset,
	'problem_template_formset' :problem_template_formset,
	'answer_formset': answer_formset,
	'variable_formset' : variable_formset,
	'script_formset' : script_formset,
	'common_introduction_formset' : common_introduction_formset,
	'hint_formset' : hint_formset,	
    	}
	c.update(csrf(request))
	return render_to_response('edit.html', c)


def edit_simple(request, problem_id):

	problem = get_object_or_404(Problem, id=problem_id)

	class RequiredFormSet(BaseFormSet):
		def __init__(self, *args, **kwargs):
			super(RequiredFormSet, self).__init__(*args, **kwargs)
			for form in self.forms:
				form.empty_permitted = True
				
	class MyInline(BaseInlineFormSet): 
   		def __init__(self, *args, **kwargs): 
			super(MyInline, self).__init__(*args, **kwargs) 
			self.can_delete = False 

	
	
	maxpt = max(0, len(ProblemTemplate.objects.filter(problem=problem)))
	ProblemTemplateInlineFormSet = inlineformset_factory(Problem, ProblemTemplate, max_num=maxpt)
	
	maxa = max(0, len(Answer.objects.filter(problem=problem)))
	AnswerInlineFormSet = inlineformset_factory(Problem, Answer, max_num =maxa)

	maxch = max(0, len(Choice.objects.filter(problem=problem)))
	ChoiceInlineFormSet = inlineformset_factory(Problem, Choice, max_num=maxch, formset=MyInline)
	
	maxh = max(0, len(Hint.objects.filter(problem=problem)))
	HintInlineFormSet = inlineformset_factory(Problem, Hint, max_num=maxh)
	

	if request.method == 'POST':
		problem_form =ProblemForm(request.POST, instance=problem)
		problem_template_formset = ProblemTemplateInlineFormSet(request.POST,request.FILES, instance=problem, prefix='templates')
		

		answer_formset = AnswerInlineFormSet(request.POST, instance=problem, prefix='answer')

		hint_formset = HintInlineFormSet(request.POST, request.FILES, instance=problem,  prefix='hints')
		choice_formset = ChoiceInlineFormSet(request.POST, request.FILES,  instance=problem, prefix='choices')
		
		if problem_form.is_valid() and problem_template_formset.is_valid() and choice_formset.is_valid() and hint_formset.is_valid() and answer_formset.is_valid():
			problem = problem_form.save()
			answer_formset.save(commit = False)
			problem_template_formset.save(commit =False)
			for form in hint_formset.forms:
				hint = form.save(commit = False)
				hint.problem = problem
				hint.save()

			for form in choice_formset.forms:
				choice = form.save(commit = False)
				choice.problem = problem
				choice.save()
			return HttpResponseRedirect('/qbank/problems/')
	else:
		problem_form = ProblemForm(instance=problem)
		
		problem_template_formset = ProblemTemplateInlineFormSet( instance=problem, prefix='templates')

		choice_formset = ChoiceInlineFormSet(instance=problem, prefix='choices')
		
		answer_formset = AnswerInlineFormSet(instance=problem, prefix='answer')
		hint_formset = HintInlineFormSet( instance=problem, prefix='hints')

		
	c = {
	'problem_form' : problem_form,
	'choice_formset' : choice_formset,
	'problem_template_formset' :problem_template_formset,
	'answer_formset': answer_formset,
	'hint_formset' : hint_formset,	
    	}
	c.update(csrf(request))
	return render_to_response('simple.html', c)

def edit_list(request, problem_id):

	problem = get_object_or_404(Problem, id=problem_id)

	class RequiredFormSet(BaseFormSet):
		def __init__(self, *args, **kwargs):
			super(RequiredFormSet, self).__init__(*args, **kwargs)
			for form in self.forms:
				form.empty_permitted = True
	class MyInline(BaseInlineFormSet): 
   		def __init__(self, *args, **kwargs): 
			super(MyInline, self).__init__(*args, **kwargs) 
			self.can_delete = False 

	
	
	maxpt = max(0, len(ProblemTemplate.objects.filter(problem=problem)))
	ProblemTemplateInlineFormSet = inlineformset_factory(Problem, ProblemTemplate, max_num=maxpt)
	maxa = max(0, len(Answer.objects.filter(problem=problem)))

	AnswerInlineFormSet = inlineformset_factory(Problem, Answer, max_num =maxa)
	maxv = max(0, len(Variable.objects.filter(problem=problem)))

	VariableInlineFormSet = inlineformset_factory(Problem, Variable, max_num=maxv)
		
	maxh = max(0, len(Hint.objects.filter(problem=problem)))

	HintInlineFormSet = inlineformset_factory(Problem, Hint, max_num=maxh)
	if request.method == 'POST':
		problem_form =ProblemForm(request.POST, instance=problem)
		problem_template_formset = ProblemTemplateInlineFormSet(request.POST,request.FILES, instance=problem, prefix='templates')

		answer_formset = AnswerInlineFormSet(request.POST, instance=problem, prefix='answer')

		hint_formset = HintInlineFormSet(request.POST, request.FILES, instance=problem,  prefix='hints')
		variable_formset = VariableInlineFormSet(request.POST, request.FILES,instance=problem, prefix='variables')
		
		if problem_form.is_valid() and variable_formset.is_valid() and problem_template_formset.is_valid() and hint_formset.is_valid() and answer_formset.is_valid():
			problem = problem_form.save()
			answer_formset.save(commit = False)
			problem_template_formset.save(commit =False)
			variable_formset.save(commit = False)


			for form in hint_formset.forms:
				hint = form.save(commit = False)
				hint.problem = problem
				hint.save()


			return HttpResponseRedirect('/qbank/problems/')
	else:
		problem_form = ProblemForm(instance=problem)
		
		problem_template_formset = ProblemTemplateInlineFormSet( instance=problem, prefix='templates')

		answer_formset = AnswerInlineFormSet(instance=problem, prefix='answer')
		
		variable_formset = VariableInlineFormSet(instance=problem, prefix='variables')
		hint_formset = HintInlineFormSet( instance=problem, prefix='hints')

		
	c = {
	'problem_form' : problem_form,
	'problem_template_formset' :problem_template_formset,
	'answer_formset': answer_formset,
	'variable_formset' : variable_formset,
	'hint_formset' : hint_formset,	
    	}
	c.update(csrf(request))
	return render_to_response('list.html', c, context_instance=RequestContext(request))


def edit_range(request, problem_id):

	problem = get_object_or_404(Problem, id=problem_id)

	class RequiredFormSet(BaseFormSet):
		def __init__(self, *args, **kwargs):
			super(RequiredFormSet, self).__init__(*args, **kwargs)
			for form in self.forms:
				form.empty_permitted = True
	class MyInline(BaseInlineFormSet): 
   		def __init__(self, *args, **kwargs): 
			super(MyInline, self).__init__(*args, **kwargs) 
			self.can_delete = False 

	
	
	maxpt = max(0, len(ProblemTemplate.objects.filter(problem=problem)))
	ProblemTemplateInlineFormSet = inlineformset_factory(Problem, ProblemTemplate, max_num=maxpt)
	maxa = max(0, len(Answer.objects.filter(problem=problem)))

	AnswerInlineFormSet = inlineformset_factory(Problem, Answer, max_num =maxa)
	maxv = max(0, len(Variable.objects.filter(problem=problem)))

	VariableInlineFormSet = inlineformset_factory(Problem, Variable, max_num=maxv)
		
	
	if request.method == 'POST':
		problem_form =ProblemForm(request.POST, instance=problem)
		problem_template_formset = ProblemTemplateInlineFormSet(request.POST,request.FILES, instance=problem, prefix='templates')

		answer_formset = AnswerInlineFormSet(request.POST, instance=problem, prefix='answer')

		variable_formset = VariableInlineFormSet(request.POST, request.FILES, prefix='variables', instance=problem)
		
		if problem_form.is_valid() and variable_formset.is_valid() and problem_template_formset.is_valid() and answer_formset.is_valid():
			problem = problem_form.save()
			answer_formset.save(commit = False)
			problem_template_formset.save(commit =False)

			for form in variable_formset.forms:
				variable = form.save(commit = False)
				variable.problem = problem
				variable.save()

			return HttpResponseRedirect('/qbank/problems/')
	else:
		problem_form = ProblemForm(instance=problem)
		
		problem_template_formset = ProblemTemplateInlineFormSet(instance=problem, prefix='templates')

		answer_formset = AnswerInlineFormSet(instance=problem, prefix='answer')
		
		variable_formset = VariableInlineFormSet(instance=problem, prefix='variables')


		
	c = {
	'problem_form' : problem_form,
	'problem_template_formset' :problem_template_formset,
	'answer_formset': answer_formset,
	'variable_formset' : variable_formset,
    	}
	c.update(csrf(request))
	return render_to_response('range.html', c)


def edit_summative(request, problem_id):

	problem = get_object_or_404(Problem, id=problem_id)

	class RequiredFormSet(BaseFormSet):
		def __init__(self, *args, **kwargs):
			super(RequiredFormSet, self).__init__(*args, **kwargs)
			for form in self.forms:
				form.empty_permitted = True
	class MyInline(BaseInlineFormSet): 
   		def __init__(self, *args, **kwargs): 
			super(MyInline, self).__init__(*args, **kwargs) 
			self.can_delete = False 

	
	problems = Problem.objects.all()
	maxpt = max(0, len(ProblemTemplate.objects.filter(problem=problem)))
	ProblemTemplateInlineFormSet = inlineformset_factory(Problem, ProblemTemplate, max_num=maxpt)
	maxc = max(0, len(CommonIntroduction.objects.filter(problem=problem)))

	CommonIntroductionFormSet =  inlineformset_factory(Problem, CommonIntroduction, max_num =maxc )

	if request.method == 'POST':
		problem_form =ProblemForm(request.POST, instance=problem)
		problem_template_formset = ProblemTemplateInlineFormSet(request.POST,request.FILES, prefix='templates', instance=problem )
		common_introduction_formset = CommonIntroductionFormSet(request.POST, request.FILES, prefix='common_intro', instance =problem)
		
		if problem_form.is_valid() and problem_template_formset.is_valid() and common_introduction_formset.is_valid() :
			problem = problem_form.save()
			
			common_introduction_formset.save(commit = False)
			
			for form in problem_template_formset.forms:
				problem_template = form.save(commit = False)
				problem_template.problem = problem
				problem_template.save()

			
			return HttpResponseRedirect('/qbank/problems/')
	else:
		problem_form = ProblemForm(instance=problem)
		
		problem_template_formset = ProblemTemplateInlineFormSet(instance=problem, prefix='templates')
		common_introduction_formset = CommonIntroductionFormSet(instance=problem, prefix='common_intro')

		
	c = {
	'problem_form' : problem_form,
	'problem_template_formset' :problem_template_formset,
	'common_introduction_formset' : common_introduction_formset,
	'problems' : problems,
    	}
	c.update(csrf(request))
	return render_to_response('summative.html', c)


#@login_required
def summative_details(request, problem_id):
	p = get_object_or_404(Problem, id=problem_id)
	q = ProblemTemplate.objects.filter(problem = p)
#	v = Variable.objects.get(problem = p)
#	s = Answer.objects.get(problem = p)
#	c = Choice.objects.get(problem = p)
#	h = p.hint_set.all()
	destination = open('/home/OpenDSA/exercises/'+p.title+'.html', 'wb+')

	str ="<!DOCTYPE html>"+"\n"+"<html data-require=\"math math-format word-problems spin\">"+"\n"+"<head>"+"\n"+"<title>"+"\n"+p.title+"</title>"+"\n"+"<script src=\"../../lib/jquery.min.js\">"+"\n"+"</script>"+"\n"+"<script src=\"../../lib/jquery-ui.min.js\">"+"\n"+"</script>"+"\n"+"<script>urlBaseOverride = \"../../ODSAkhan-exercises/\";</script>"+"\n"+"<script src=\"../../lib/khan-exercise-min.js\">"+"\n"+"</script>"+"\n"+"</head>"+"\n"+"<body>"+"\n"
	
	for c in p.problemtemplate_set.all():
		str += "<div class=\"exercise\" data-name=\"/exercises/"
		str += c.question
		str += "\">"
		str += "</div>"+"\n"
	str += "</body>"+"\n"+"<script type=\"text/javascript\"src=\"http://cdn.mathjax.org/mathjax/1.1-latest/MathJax.js?config=http://algoviz.org/OpenDSA/ODSAkhan-exercises/KAthJax-77111459c7d82564a705f9c5480e2c88.js\">"+"\n"+"</script>"+"</html>"
	destination.write(bytes(str))
	destination.close()


	context = Context({
		'p':p,
		'title':p.title,	
		'question':q,
	#	'solution':s,
	#	'choice':c,
#		'hint':h
	})
	return render_to_response('summative_details.html', context)

#@login_required
def ka_gen(request, problem_id):

	p = get_object_or_404(Problem, id=problem_id)
	q = ProblemTemplate.objects.filter(problem = p)
#	v = Variable.objects.get(problem = p)
#	s = Answer.objects.get(problem = p)
#	c = Choice.objects.get(problem = p)
#	h = p.hint_set.all()
	destination = open('/home/OpenDSA/temp/'+p.title+'_View.html', 'wb+')

	str ="<!DOCTYPE html>"+"\n"+"<html data-require=\"math math-format word-problems spin\">"+"\n"+"<head>"+"\n"+"<title>"+"\n"+p.title+"</title>"+"\n"+"<script src=\"../../lib/jquery.min.js\">"+"\n"+"</script>"+"\n"+"<script src=\"../../lib/jquery-ui.min.js\">"+"\n"+"</script>"+"\n"+"<script>urlBaseOverride = \"../../ODSAkhan-exercises/\";</script>"+"\n"+"<script src=\"../../lib/khan-exercise-min.js\">"+"\n"+"</script>"+"\n"+"</head>"+"\n"+"<body>"+"\n"
	
	
	str += "<div class=\"exercise\" data-name=\"/exercises/"
	str += p.title
	str += "\">"
	str += "\n"+"</div>"
	str +="</body>"+"\n"+"<script type=\"text/javascript\" src=\"http://cdn.mathjax.org/mathjax/1.1-latest/MathJax.js?config=http://algoviz.org/OpenDSA/ODSAkhan-exercises/KAthJax-77111459c7d82564a705f9c5480e2c88.js\">"+"</script>"+"</html>"
	destination.write(bytes(str))
	destination.close()


	context = Context({
		'p':p,
		'title':p.title,	
		'question':q,
	#	'solution':s,
	#	'choice':c,
#		'hint':h
	})
	return render_to_response('ka_gen.html', context)

#@login_required
def range_details(request, problem_id):
	p = get_object_or_404(Problem, id=problem_id)
	q = ProblemTemplate.objects.get(problem = p)
#	v = Variable.objects.get(problem = p)
	s = Answer.objects.get(problem = p)
#	c = Choice.objects.get(problem = p)
	h = p.hint_set.all()
	destination = open('/home/OpenDSA/exercises/'+p.title+'.html', 'wb+')

	str ="<!DOCTYPE html>"+"\n"+"<html data-require=\"math math-format word-problems spin\">"+"\n"+"<head>"+"\n"+"<title>"+"\n"+p.title+"</title>"+"\n"+"<script src=\"../../lib/jquery.min.js\">"+"\n"+"</script>"+"\n"+"<script src=\"../../lib/jquery-ui.min.js\">"+"\n"+"</script>"+"\n"+"<script>urlBaseOverride = \"../../ODSAkhan-exercises/\";</script>"+"\n"+"<script src=\"../../lib/khan-exercise-min.js\">"+"\n"+"</script>"+"\n"+"</head>"+"\n"+"<body>"+"\n"+"<div class=\"exercise\"><div class=\"vars\">" 
	for t in p.variable_set.all():
		str +="<var id=\""
		str += t.var_id
		str += "\">"	
		str += t.var_value
		str += "</var>"+"\n"

	str += "</div>"+"\n"+"<div class=\"problems\"> "+"\n"+"<div id=\"problem-type-or-description\">"+"\n"+"<p class=\"problem\">"+"\n"+"<p class=\"question\">"
	str += q.question
	str += "</p>"+"\n"+"<div class=\"solution\""
	if "log" in q.question:
		str += "data-forms=\"log\""
	str += ">"+"\n"+"<var>"
	str += s.solution
	str += "</var>"+"\n"+"</div>"+"\n"+"</div>"+"\n"+"</div>"+"\n"+"</div>"+"\n"+"</body>"+"\n"+"<script type=\"text/javascript\"src=\"http://cdn.mathjax.org/mathjax/1.1-latest/MathJax.js?config=http://algoviz.org/OpenDSA/ODSAkhan-exercises/KAthJax-77111459c7d82564a705f9c5480e2c88.js\">"+"\n"+"</script>"+"</html>"

	destination.write(bytes(str))

	destination.close()

	context = Context({
		'p':p,
		'title':p.title,	
		'question':q,
		'solution':s,
	#	'choice':c,
#		'hint':h
	})
	return render_to_response('range_details.html', context)

#@login_required
def list_details(request, problem_id):
	p = get_object_or_404(Problem, id=problem_id)
	q = ProblemTemplate.objects.get(problem = p)
#	v = Variable.objects.get(problem = p)
	s = Answer.objects.get(problem = p)
#	c = Choice.objects.get(problem = p)
	h = p.hint_set.all()

	destination = open('/home/OpenDSA/exercises/'+p.title+'.html', 'wb+')
	str ="<!DOCTYPE html>"+"\n"+"<html data-require=\"math math-format word-problems spin\">"+"\n"+"<head>"+"\n"+"<title>"+"\n"+p.title+"</title>"+"\n"+"<script src=\"../../lib/jquery.min.js\">"+"\n"+"</script>"+"\n"+"<script src=\"../../lib/jquery-ui.min.js\">"+"\n"+"</script>"+"\n"+"<script>urlBaseOverride = \"../../ODSAkhan-exercises/\";</script>"+"\n"+"<script src=\"../../lib/khan-exercise-min.js\">"+"\n"+"</script>"+"\n"+"</head>"+"\n"+"<body>"+"\n"+"<div class=\"exercise\">"+"\n"+"<div class=\"vars\">"+"\n"

	solution_list = (s.solution).split(",")
	index = 1
	ans_uniq = []
	for t in solution_list:
		if t not in ans_uniq:
			ans_uniq.append(t)
			j = "A%d" %index
			str += "<var id=\""
			str += j
			str += "\">"+t
			str += "</var>"+"\n"
			index = index +1




	count = 0
	var_count_array = []
	for t in p.variable_set.all():
		str +="<var id=\""
		str += t.var_id
		str += "\">["	
		str += t.var_value
		str += "]</var>"+"\n"

		j = "x%d" %(count+1)
		var_elements = (t.var_value).split(",")
		var_count_array.append(len(var_elements))

		str += "<var id=\""
		str += j
		str += "\">randRange(0,%d" %(len(var_elements) -1)
		str += ")</var>"+"\n"
		count = count+1


	eq = "x%d" % len(var_count_array)
	var_count = count -1
	coef = 1
	while (var_count>0):
		coef = coef * var_count_array[var_count]
		var = "%d" %coef
		var += "*x"
		var += "%d" %var_count
		eq = var +"+"+eq 
		var_count = var_count-1 
		
		
		
	str += "<var id =\"INDEX\">"
	str += eq
	str += "</var>"+"\n"

	str += "<var id=\"ANSWER\">["	
	
	str += s.solution
	str += "]</var>"+"\n"

	str += "</div>"+"\n"+"<div class=\"problems\"> "+"\n"+"<div id=\"problem-type-or-description\">"+"\n"+"<p class=\"problem\">"+"\n"+"<p class=\"question\">"
	str += q.question
	str += "</p>"+"\n"+"<div class=\"solution\"><var>ANSWER[INDEX]</var>"+"\n"+"</div>"+"\n"+"<ul class =\"choices\" data-category=\"true\">"
	num=1

	answer_unique = []
	for t in solution_list:
		if t not in answer_unique:
			answer_unique.append(t)		
			str += "<li><var>"
			str += "A%d" %num
			num = num +1
			str += "</var></li>"
	str += "</ul>"
	str += "<div class=\"hints\">"
	for h in p.hint_set.all():
		str += "<p>\""
		str += h.hint
		str += "\"</p>"
	str += "</div>"

	str += "</div>"+"\n"+"</div>"+"\n"+"</div>"+"\n"+"</body>"+"\n"+"<script type=\"text/javascript\"src=\"http://cdn.mathjax.org/mathjax/1.1-latest/MathJax.js?config=http://algoviz.org/OpenDSA/ODSAkhan-exercises/KAthJax-77111459c7d82564a705f9c5480e2c88.js\">"+"\n"+"</script>"+"</html>"



	
	destination.write(bytes(str))

	destination.close()


	context = Context({
		'p':p,
		'title':p.title,	
		'question':q,
		'solution':s,
	#	'choice':c,
#		'hint':h
	})
	return render_to_response('list_details.html', context, context_instance=RequestContext(request))

#@login_required
def write_file(request, problem_id):
	response = HttpResponse( content_type = 'text/csv')
	p = get_object_or_404(Problem, id=problem_id)
	response['Content-Disposition'] = 'attachment; filename="'+p.title+'.csv"'

	writer = csv.writer(response)
	problems = Problem.objects.filter(id = problem_id)
	
	for p in problems:
		writer.writerow(['TITLE',p.title])
		writer.writerow(['DIFFICULTY',p.difficulty_level])
		q = ProblemTemplate.objects.filter(problem = p)

		for t in q:
			writer.writerow(['QUESTION', t.question])

		for t in p.variable_set.all():
			writer.writerow(['VAR_NAME', t.var_id])
			writer.writerow(['VAR_VALUE', t.var_value])
			writer.writerow(['ATTR_INFO',t.attribute])

		for t in p.script_set.all():
			writer.writerow(['SCRIPT',t.script])
		for t in p.choice_set.all():
			writer.writerow(['CHOICE', t.choice])
		for t in p.hint_set.all():
			writer.writerow(['HINT', t.hint])
	try:
		c = CommonIntroduction.objects.get(problem = p)
		writer.writerow(['COMMON_INTRO', c.common_intro])
	except CommonIntroduction.DoesNotExist:
  		c = None
	try:
		s = Answer.objects.get(problem = p)
		writer.writerow(['SOLUTION', s.solution])
	except Answer.DoesNotExist:
		s = None
		
	return response

#@login_required
def delete(request, problem_id):
   p= Problem.objects.get(id = problem_id)
   p.delete()
   return HttpResponseRedirect('/qbank/problems/')

#@login_required
def simple(request):
    # This class is used to make empty formset forms required
    # See http://stackoverflow.com/questions/2406537/django-formsets-make-first-required/4951032#4951032
	class RequiredFormSet(BaseFormSet):
		def __init__(self, *args, **kwargs):
			super(RequiredFormSet, self).__init__(*args, **kwargs)
			for form in self.forms:
				form.empty_permitted = True
	HintFormSet = formset_factory(HintForm, max_num = 10, formset = RequiredFormSet)
	ChoiceFormSet = formset_factory(ChoiceForm, max_num = 10, formset = RequiredFormSet)


	if request.method == 'POST': # If the form has been submitted...
		problem_form = SimpleProblemForm(request.POST)
		problem_template_form = ProblemTemplateForm(request.POST, prefix='template')
		answer_form = AnswerForm(request.POST, prefix='answer')

		hint_formset = HintFormSet(request.POST, request.FILES, prefix='hints')
		
		choice_formset = ChoiceFormSet(request.POST, request.FILES, prefix='choices')
		if problem_form.is_valid() and problem_template_form.is_valid() and choice_formset.is_valid() and hint_formset.is_valid() and answer_form.is_valid():
			problem = problem_form.save()
			problem_template = problem_template_form.save(commit = False)
			problem_template.problem = problem
			problem_template.save()

			answer = answer_form.save(commit = False)
			answer.problem = problem
			answer.save()
			for form in hint_formset.forms:
				hint = form.save(commit = False)
				hint.problem = problem
				hint.save()
			for form in choice_formset.forms:
				choice = form.save(commit = False)
				choice.problem = problem
				choice.save() # Redirect to a 'success' page
			return HttpResponseRedirect('/qbank/problems/')
	else:
		problem_form = SimpleProblemForm()
		choice_formset = ChoiceFormSet(prefix='choices')
		problem_template_form = ProblemTemplateForm(prefix='template')
		answer_form = AnswerForm(prefix='answer')
		hint_formset = HintFormSet(prefix='hints')
	c = {'problem_form' : problem_form,
	     'choice_formset' : choice_formset,
	     'problem_template_form' : problem_template_form,
	     'answer_form': answer_form,
	     'hint_formset' : hint_formset,
	}
	c.update(csrf(request))
	return render_to_response('simple.html', c)

#@login_required
def list(request):
    # This class is used to make empty formset forms required
    # See http://stackoverflow.com/questions/2406537/django-formsets-make-first-required/4951032#4951032
	class RequiredFormSet(BaseFormSet):
		def __init__(self, *args, **kwargs):
			super(RequiredFormSet, self).__init__(*args, **kwargs)
			for form in self.forms:
				form.empty_permitted = True
	VariableFormSet = formset_factory(VariableForm, max_num = 10, formset = RequiredFormSet)
	HintFormSet = formset_factory(HintForm, max_num = 10, formset = RequiredFormSet)
	#ChoiceFormSet = formset_factory(ChoiceForm, max_num = 10, formset = RequiredFormSet)


	if request.method == 'POST': # If the form has been submitted...
		problem_form = ListProblemForm(request.POST)
		problem_template_form = ProblemTemplateForm(request.POST, prefix='template')
		answer_form = AnswerForm(request.POST, prefix='answer')
		variable_formset = VariableFormSet(request.POST,request.FILES, prefix='variables')
		hint_formset = HintFormSet(request.POST, request.FILES, prefix='hints')
		#choice_formset = ChoiceFormSet(request.POST, request.FILES, prefix='choices')
		if problem_form.is_valid() and problem_template_form.is_valid() and variable_formset.is_valid() and hint_formset.is_valid() and answer_form.is_valid():
			problem = problem_form.save()
			problem_template = problem_template_form.save(commit = False)
			problem_template.problem = problem
			problem_template.save()

			answer = answer_form.save(commit = False)
			answer.problem = problem
			answer.save()

            	
			for form in variable_formset.forms:
				variable = form.save(commit = False)
				variable.problem = problem
				variable.save() # Redirect to a 'success' page
			for form in hint_formset.forms:
				hint = form.save(commit = False)
				hint.problem = problem
				hint.save()
			
			return HttpResponseRedirect('/qbank/problems/')
	else:
		problem_form = ListProblemForm()
		problem_template_form = ProblemTemplateForm(prefix='template')
		answer_form = AnswerForm(prefix='answer')	
		variable_formset = VariableFormSet(prefix='variables')
		#choice_formset = ChoiceFormSet(prefix='choices')
		hint_formset = HintFormSet(prefix='hints')
	c = {'problem_form' : problem_form,
	     'problem_template_form' : problem_template_form,
	     'answer_form': answer_form,
	     'variable_formset' : variable_formset,
	     #'choice_formset': choice_formset,
	     'hint_formset' : hint_formset,
	}
	c.update(csrf(request))
	return render_to_response('list.html', c)

#@login_required
def range(request):
    # This class is used to make empty formset forms required
    # See http://stackoverflow.com/questions/2406537/django-formsets-make-first-required/4951032#4951032
	class RequiredFormSet(BaseFormSet):
		def __init__(self, *args, **kwargs):
			super(RequiredFormSet, self).__init__(*args, **kwargs)
			for form in self.forms:
				form.empty_permitted = True
	VariableFormSet = formset_factory(VariableForm, max_num = 10, formset = RequiredFormSet)
	if request.method == 'POST': # If the form has been submitted...
		problem_form = RangeProblemForm(request.POST)
		problem_template_form = ProblemTemplateForm(request.POST, prefix='template')
		answer_form = AnswerForm(request.POST, prefix='answer')
		variable_formset = VariableFormSet(request.POST,request.FILES, prefix='variables')
		if problem_form.is_valid() and problem_template_form.is_valid() and variable_formset.is_valid() and answer_form.is_valid():
			problem = problem_form.save()
			problem_template = problem_template_form.save(commit = False)
			problem_template.problem = problem
			problem_template.save()

			answer = answer_form.save(commit = False)
			answer.problem = problem
			answer.save()

            	
			for form in variable_formset.forms:
				variable = form.save(commit = False)
				variable.problem = problem
				variable.save() # Redirect to a 'success' page
			return HttpResponseRedirect('/qbank/problems/')
	else:
		problem_form = RangeProblemForm()
		problem_template_form = ProblemTemplateForm(prefix='template')
		answer_form = AnswerForm(prefix='answer')	
		variable_formset = VariableFormSet(prefix='variables')
	c = {'problem_form' : problem_form,
	     'problem_template_form' : problem_template_form,
	     'answer_form': answer_form,
	     'variable_formset' : variable_formset,
	}
	c.update(csrf(request))
	return render_to_response('range.html', c)





def summative(request):
    # This class is used to make empty formset forms required
    # See http://stackoverflow.com/questions/2406537/django-formsets-make-first-required/4951032#4951032
	class RequiredFormSet(BaseFormSet):
		def __init__(self, *args, **kwargs):
			super(RequiredFormSet, self).__init__(*args, **kwargs)
			for form in self.forms:
				form.empty_permitted = True

	problems = Problem.objects.all()
	ProblemTemplateFormSet = formset_factory(ProblemTemplateForm, max_num = 10, formset = RequiredFormSet)
	if request.method == 'POST': # If the form has been submitted...
		problem_form = SummativeProblemForm(request.POST)
		common_introduction_form = CommonIntroductionForm(request.POST, prefix='common_intro')
		problem_template_formset = ProblemTemplateFormSet(request.POST, request.FILES, prefix='templates')
		if problem_form.is_valid() and common_introduction_form.is_valid() and problem_template_formset.is_valid():
			problem = problem_form.save()
			common_intro = common_introduction_form.save(commit=False)
			common_intro.problem = problem
			common_intro.save()

			for form in problem_template_formset.forms:
				problem_template = form.save(commit=False)
				problem_template.problem = problem
				problem_template.save() # Redirect to a 'success' page
			return HttpResponseRedirect('/qbank/problems/')
	else:
		problem_form = SummativeProblemForm()
		common_introduction_form = CommonIntroductionForm(prefix='common_intro')
		problem_template_formset = ProblemTemplateFormSet(prefix='templates')
	c = {'problem_form' : problem_form,
		'common_introduction_form' : common_introduction_form,
		'problem_template_formset' : problem_template_formset,
		'problems':problems,
	}
	c.update(csrf(request))
	return render_to_response('summative.html', c)


def d(request, problem_id):
    p = get_object_or_404(Problem, id=problem_id)
    file_path = "/home/OpenDSA/exercises/"+p.title+".html"
    try:
	file_wrapper = FileWrapper(file(file_path,'rb'))
    except:
	return HttpResponseRedirect('ka_error/')
    file_mimetype = mimetypes.guess_type(file_path)
    response = HttpResponse(file_wrapper, content_type=file_mimetype)
    response['X-Sendfile'] = file_path
    response['Content-Length'] = os.stat(file_path).st_size
    response['Content-Disposition'] = 'attachment; filename=%s/' % smart_str(p.title) 
    return response


def server_error(request, template_name = '500.html'):
	return render_to_response(template_name,context_instance = RequestContext(request)) 

