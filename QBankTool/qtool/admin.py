from qtool.models import *
from django.contrib import admin

admin.site.register(Variable)
admin.site.register(Choice)
admin.site.register(ProblemTemplate)
admin.site.register(Answer)

class VariableAdmin(admin.TabularInline):
	model = Variable
	extra = 0

class ChoiceAdmin(admin.TabularInline):
	model = Choice
	extra = 0

class ProblemTemplateAdmin(admin.TabularInline):
	model = ProblemTemplate
	extra = 0

class AnswerAdmin(admin.TabularInline):
	model = Answer
	extra = 0

class HintAdmin(admin.TabularInline):
	model = Hint
	extra = 0

class ProblemAdmin(admin.ModelAdmin):
	inlines = [VariableAdmin, ProblemTemplateAdmin, ChoiceAdmin, HintAdmin, AnswerAdmin ]

admin.site.register(Problem, ProblemAdmin)


