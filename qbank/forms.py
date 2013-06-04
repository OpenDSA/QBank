from qbank.models import *
from django.forms import ModelForm

from django import forms

class VariableForm(ModelForm):
	class Meta:
		model = Variable
		widgets = {
			'var_value': forms.Textarea(attrs={'rows': 2, 'cols': 20}),
			'attribute': forms.Textarea(attrs={'class' : 'myfieldclass', 'required' : 'False'}),
			
			}
		exclude = ('problem',)
	def __init__(self, *arg, **kwarg):
		super(VariableForm, self).__init__(*arg, **kwarg)
		self.empty_permitted = True

class CommonIntroductionForm(ModelForm):
	class Meta:
		model = CommonIntroduction
		widgets = {
          		'common_intro': forms.Textarea(attrs={'class' : 'myfieldclass'}),
        	}
		exclude = ('problem',)
	def __init__(self, *arg, **kwarg):
		super(CommonIntroductionForm, self).__init__(*arg, **kwarg)
		self.empty_permitted = True


class ProblemTemplateForm(ModelForm):
	class Meta:
		model = ProblemTemplate
		widgets = {
          		'question': forms.Textarea(attrs={'class' : 'myfieldclass'}),
        	}
		exclude = ('problem',)
	def __init__(self, *arg, **kwarg):
		super(ProblemTemplateForm, self).__init__(*arg, **kwarg)
		self.empty_permitted = True


class AnswerForm(ModelForm):
	class Meta:
		model = Answer
		widgets = {
          		'solution': forms.Textarea(attrs={'class' : 'myfieldclass'}),
        	}
		exclude = ('problem',)
	def __init__(self, *arg, **kwarg):
		super(AnswerForm, self).__init__(*arg, **kwarg)
		self.empty_permitted = True



class ChoiceForm(ModelForm):
	class Meta:
		model = Choice
		widgets = {
          		'choice': forms.Textarea(attrs={'class' : 'myfieldclass'}),
        	}
		exclude = ('problem',)
	def __init__(self, *arg, **kwarg):
		super(ChoiceForm, self).__init__(*arg, **kwarg)
		self.empty_permitted = True



class HintForm(ModelForm):
	class Meta:
		model = Hint
		widgets = {
          		'hint': forms.Textarea(attrs={'class' : 'myfieldclass'}),
        	}
		exclude = ('problem',)
	def __init__(self, *arg, **kwarg):
		super(HintForm, self).__init__(*arg, **kwarg)
		self.empty_permitted = True


class ScriptForm(ModelForm):
	class Meta:
		model = Script
		widgets = {
          		'script': forms.Textarea(attrs={'class' : 'myfieldclass'}),
        	}
		exclude = ('problem',)
	def __init__(self, *arg, **kwarg):
		super(ScriptForm, self).__init__(*arg, **kwarg)
		self.empty_permitted = True


class ProblemForm(ModelForm):
	type = forms.CharField(widget = forms.HiddenInput(), initial ='KA')
	class Meta:
		model = Problem
	def __init__(self, *arg, **kwarg):
		super(ProblemForm, self).__init__(*arg, **kwarg)
		self.empty_permitted = True

		


class SimpleProblemForm(ModelForm):
	type = forms.CharField(widget = forms.HiddenInput(), initial ='Simple')
	class Meta:
		model = Problem
		exclude = ('variable','script')
	def __init__(self, *arg, **kwarg):
		super(SimpleProblemForm, self).__init__(*arg, **kwarg)
		self.empty_permitted = True


class ListProblemForm(ModelForm):
	type = forms.CharField(widget = forms.HiddenInput(), initial ='List')
	class Meta:
		model = Problem
		exclude = ('script', 'choice','hint')
	def __init__(self, *arg, **kwarg):
		super(ListProblemForm, self).__init__(*arg, **kwarg)
		self.empty_permitted = True



class RangeProblemForm(ModelForm):
	type = forms.CharField(widget = forms.HiddenInput(), initial ='Range')
	class Meta:
		model = Problem
		exclude = ('script', 'choice','hint')
	def __init__(self, *arg, **kwarg):
		super(RangeProblemForm, self).__init__(*arg, **kwarg)
		self.empty_permitted = True



		
class SummativeProblemForm(ModelForm):
	type = forms.CharField(widget = forms.HiddenInput(), initial ='Summative')
	class Meta:
		model = Problem
		exclude = ('script', 'choice','hint', 'answer', 'variable')
	def __init__(self, *arg, **kwarg):
		super(SummativeProblemForm, self).__init__(*arg, **kwarg)
		self.empty_permitted = True

