<script setup>
import Layout from '../../Layout/App.vue';
import { useForm } from '@inertiajs/vue3'

import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import { Badge } from '@/components/ui/badge'
import {
  DropdownMenu,
  DropdownMenuCheckboxItem,
  DropdownMenuContent,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Plus } from 'lucide-vue-next'
import { Toaster } from '@/components/ui/toast'
import { useToast } from '@/components/ui/toast/use-toast'

import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'

import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'

import { 
  TagsInput, 
  TagsInputInput, 
  TagsInputItem, 
  TagsInputItemDelete, 
  TagsInputItemText 
} from '@/components/ui/tags-input'

import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { valueUpdater } from '@/lib/utils'
import { CaretSortIcon, ChevronDownIcon } from '@radix-icons/vue'
import {
  FlexRender,
  getCoreRowModel,
  getExpandedRowModel,
  getFilteredRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  useVueTable,
} from '@tanstack/vue-table'
import { h, ref } from 'vue'
import DropdownAction from './DataTableDemoColumn.vue'

const { toast } = useToast()

const props = defineProps({
  data: Array
});

const data = props.data;

const columns = [
  {
    id: 'select',
    header: ({ table }) => h(Checkbox, {
      'checked': table.getIsAllPageRowsSelected() || (table.getIsSomePageRowsSelected() && 'indeterminate'),
      'onUpdate:checked': value => table.toggleAllPageRowsSelected(!!value),
      'ariaLabel': 'Select all',
    }),
    cell: ({ row }) => h(Checkbox, {
      'checked': row.getIsSelected(),
      'onUpdate:checked': value => row.toggleSelected(!!value),
      'ariaLabel': 'Select row',
    }),
    enableSorting: false,
    enableHiding: false,
  },
  {
    accessorKey: 'name',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Name', h(CaretSortIcon, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', { class: 'lowercase' }, row.getValue('name')),
  },
  {
    accessorKey: 'title',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Title', h(CaretSortIcon, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', { class: 'lowercase' }, row.getValue('title')),
  },
  {
    accessorKey: 'price',
    header: () => h('div', { class: 'text-right' }, 'Price'),
    cell: ({ row }) => {
      const price = Number.parseFloat(row.getValue('price'))

      // Format the price as a dollar price
      const formatted = new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
      }).format(price)

      return h('div', { class: 'text-right font-medium' }, formatted)
    },
  },
  {
    accessorKey: 'is_active',
    header: 'Status',
    cell: ({ row }) => {
        const status = row.getValue('is_active');
        if(status) {
            return h(Badge, 'Active')
        } else {
            return h(Badge, { variant: 'outline' },  'Inactive')
        }
    },
  },
  {
    id: 'actions',
    enableHiding: false,
    cell: ({ row }) => {
      const payment = row.original

      return h(DropdownAction, {
        payment,
        onEdit,
        onDeleteProduct,
        onExpand: row.toggleExpanded,
      })
    },
  },
]

const sorting = ref([])
const columnFilters = ref([])
const columnVisibility = ref({})
const rowSelection = ref({})
const expanded = ref({})

const table = useVueTable({
  data,
  columns,
  getCoreRowModel: getCoreRowModel(),
  getPaginationRowModel: getPaginationRowModel(),
  getSortedRowModel: getSortedRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  getExpandedRowModel: getExpandedRowModel(),
  onSortingChange: updaterOrValue => valueUpdater(updaterOrValue, sorting),
  onColumnFiltersChange: updaterOrValue => valueUpdater(updaterOrValue, columnFilters),
  onColumnVisibilityChange: updaterOrValue => valueUpdater(updaterOrValue, columnVisibility),
  onRowSelectionChange: updaterOrValue => valueUpdater(updaterOrValue, rowSelection),
  onExpandedChange: updaterOrValue => valueUpdater(updaterOrValue, expanded),
  state: {
    get sorting() { return sorting.value },
    get columnFilters() { return columnFilters.value },
    get columnVisibility() { return columnVisibility.value },
    get rowSelection() { return rowSelection.value },
    get expanded() { return expanded.value },
  },
})

const isEditing = ref(false);
const isCreating = ref(false);
const currentProduct = ref(null);
const showDialog = ref(false);

const showDialogCreate = () => {
  isCreating.value = true;
  isEditing.value = false;
  currentProduct.value = null;
  form.reset();

  showDialog.value = true;
}

const productSizeValue = ref(['sm', 'md', 'l', 'xl'])

const form = useForm({
  name: '',
  title: '',
  description: '',
  category_ref_id: null,
  brand_ref_id: '',
  price: 0,
  discount_price: 0,
  benefits_content: '',
  ingredients_content: '',
  howtouse_content: '',
  product_size_id: '',
  is_active: 1,
  product_image: null
});

const imagePreviewUrl = ref(null);
function handleFileChange(event) {
  const file = event.target.files[0];
  if (file) {
    form.product_image = file;
    imagePreviewUrl.value = URL.createObjectURL(file);
  }
}

const errors = ref({});

// const submit = () => {

//   form.product_size_id = productSizeValue.value.toString();
//   // form.product_image = imagePreviewUrl.value;

//   console.log(form.product_image);

//   form.post('/products/store', {
//     preserveState: true, 
//     onError: (error) => {
//       errors.value = error;
//     },
//     onSuccess: (results) => {
//       console.log(results);
//       toast({
//         title: 'Success',
//         description: 'Product created successfully',
//       });

//       showDialog.value = false;
//       // setTimeout(() => {
//       //   window.location.reload();
//       // }, 2000);
//     }
//   });
// }

const submit = () => {
  form.product_size_id = productSizeValue.value.toString();

  const formData = new FormData();
  for (const key in form) {
    formData.append(key, form[key]);
  }

  if(isEditing.value) {
    console.log('isEditing', isEditing);
    const productId = currentProduct.value;

    form.put(`/products/${productId}`, {
      body: formData,
      // preserveState: true,
      onError: (error) => {
        errors.value = error;
      },
      onSuccess: (results) => {
        // console.log(results);
        toast({
          title: 'Success',
          description: 'Product updated successfully',
        });
        showDialog.value = false;
        // setTimeout(() => {
            // window.location.reload();
        // }, 2000);

        if (imagePreviewUrl.value) {
          URL.revokeObjectURL(imagePreviewUrl.value);
          imagePreviewUrl.value = null;
        }
      }
    });
  } else {
    console.log('isEditing', isEditing);

    form.post('/products/store', {
      body: formData,
      // preserveState: true, 
      onError: (error) => {
        errors.value = error;
      },
      onSuccess: (results) => {
        console.log(results);
        toast({
          title: 'Success',
          description: 'Product created successfully',
        });
        showDialog.value = false;

        if (imagePreviewUrl.value) {
          URL.revokeObjectURL(imagePreviewUrl.value);
          imagePreviewUrl.value = null;
        }
      }
    });
  }
  
}

// const cloudName = 'dllghul9z';
// const uploadPreset = 'vue-app';
// const uploadPreset = 'cloudinary-stripe-vuets';

// const myWidget = cloudinary.createUploadWidget(
//   {
//     cloudName: cloudName,
//     uploadPreset: uploadPreset
//   },
//   (error, result) => {
//     if(!error && result && result.event == 'success')
//     {
//       console.log(result.info);
//     }
//   }
// );

// const openWidget = () => {
//   myWidget.open();
// }

const onEdit = async (id) => {
  currentProduct.value = id;
  isEditing.value = true;
  isCreating.value = false;

  try {
    const res = await fetch(`/products/${id}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json'
      }
    });

    if(!res.ok) {
      console.log('Error!');
    }

    const data = await res.json();
    console.log(data);

    form.name = data.data.name;
    form.title = data.data.title;
    form.description = data.data.description;
    form.category_ref_id = data.data.category_ref_id?.toString();
    form.brand_ref_id = data.data.brand_ref_id?.toString();
    form.price = data.data.price;
    form.discount_price = data.data.discount_price;
    form.benefits_content = data.data.benefits_content;
    form.ingredients_content = data.data.ingredients_content;
    form.howtouse_content = data.data.howtouse_content;
    form.product_size_id = data.data.product_size_id;
    form.is_active = data.data.is_active;

    showDialog.value = true;

  } catch (error) {
    console.log('Error!', error);
  }
  
};

const onDeleteProduct = async (id) => {
  form.delete(`/products/${id}`, {
      onError: (error) => {
        errors.value = error;
      },
      onSuccess: (result) => {
        console.log(result);
        toast({
          title: 'Success',
          description: 'Product deleted successfully',
        });
        showDialog.value = false;
      }
    });
  
};

</script>

<template>
    <Layout>
        <template #title>Product</template>
        <div>
            <div class="w-full">
                <div class="flex gap-2 items-center py-4">
                <Input
                    class="max-w-sm"
                    placeholder="Filter name..."
                    :model-value="table.getColumn('name')?.getFilterValue()"
                    @update:model-value=" table.getColumn('name')?.setFilterValue($event)"
                />

                <Button variant="outline" @click="showDialogCreate">
                  Create Product
                </Button>

                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                    <Button variant="outline" class="ml-auto">
                        Columns <ChevronDownIcon class="ml-2 h-4 w-4" />
                    </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                    <DropdownMenuCheckboxItem
                        v-for="column in table.getAllColumns().filter((column) => column.getCanHide())"
                        :key="column.id"
                        class="capitalize"
                        :checked="column.getIsVisible()"
                        @update:checked="(value) => {
                        column.toggleVisibility(!!value)
                        }"
                    >
                        {{ column.id }}
                    </DropdownMenuCheckboxItem>
                    </DropdownMenuContent>
                </DropdownMenu>
                </div>
                
                <div class="rounded-md border">
                <Table>
                    <TableHeader>
                    <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                        <TableHead v-for="header in headerGroup.headers" :key="header.id">
                        <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header" :props="header.getContext()" />
                        </TableHead>
                    </TableRow>
                    </TableHeader>
                    <TableBody>
                    <template v-if="table.getRowModel().rows?.length">
                        <template v-for="row in table.getRowModel().rows" :key="row.id">
                        <TableRow :data-state="row.getIsSelected() && 'selected'">
                            <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                            <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="row.getIsExpanded()">
                            <TableCell :colspan="row.getAllCells().length">
                            {{ JSON.stringify(row.original) }}
                            </TableCell>
                        </TableRow>
                        </template>
                    </template>

                    <TableRow v-else>
                        <TableCell
                        :colspan="columns.length"
                        class="h-24 text-center"
                        >
                        No results.
                        </TableCell>
                    </TableRow>
                    </TableBody>
                </Table>
                </div>

                <div class="flex items-center justify-end space-x-2 py-4">
                <div class="flex-1 text-sm text-muted-foreground">
                    {{ table.getFilteredSelectedRowModel().rows.length }} of
                    {{ table.getFilteredRowModel().rows.length }} row(s) selected.
                </div>
                <div class="space-x-2">
                    <Button
                    variant="outline"
                    size="sm"
                    :disabled="!table.getCanPreviousPage()"
                    @click="table.previousPage()"
                    >
                    Previous
                    </Button>
                    <Button
                    variant="outline"
                    size="sm"
                    :disabled="!table.getCanNextPage()"
                    @click="table.nextPage()"
                    >
                    Next
                    </Button>
                </div>
                </div>
            </div>
        </div>

        <!-- Add Product -->
        <Dialog v-model:open="showDialog">
            <DialogContent class="max-w-[825px]">
            <DialogHeader>
                <DialogTitle>{{ isEditing ? 'Update' : 'Create' }} Product</DialogTitle>
                <DialogDescription>
                  {{ isEditing ? 'Update' : 'Add new' }} product here . Click save when you're done.
                </DialogDescription>
            </DialogHeader>
            <div class="grid grid-cols-2 gap-4">
              <div class="grid gap-4 py-4">
                  <div class="grid items-center gap-2">
                    <Label for="name" class="">
                        Name
                    </Label>
                    <Input id="name" v-model="form.name" placeholder="Type your name here."/>
                    <span v-if="errors?.name" class="text-red-600 text-sm">{{ errors.name }}</span>
                  </div>
                  <div class="grid items-center gap-2">
                    <Label for="title" class="">
                        Title
                    </Label>
                    <Input id="title" v-model="form.title" placeholder="Type your title here."/>
                    <span v-if="errors?.title" class="text-red-600 text-sm">{{ errors.title }}</span>
                  </div>
                  <div class="grid items-center gap-2">
                    <Label for="description" class="">
                      Description
                    </Label>
                    <Textarea v-model="form.description" placeholder="Type your description here." />
                    <span v-if="errors?.description" class="text-red-600 text-sm">{{ errors.description }}</span>
                  </div>
                  <div class="grid items-center gap-2">
                    <Label for="category_ref_id" class="">
                      Category
                    </Label>
                    <Select id="category_ref_id" v-model="form.category_ref_id">
                      <SelectTrigger>
                        <SelectValue placeholder="Select a category" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectGroup>
                          <SelectItem value="1">
                            Category 1
                          </SelectItem>
                          <SelectItem value="2">
                            Category 2
                          </SelectItem>
                        </SelectGroup>
                      </SelectContent>
                    </Select>
                    <span v-if="errors?.category_ref_id" class="text-red-600 text-sm">{{ errors.category_ref_id }}</span>
                  </div>
                  <div class="grid items-center gap-2">
                    <Label for="brand_ref_id" class="">
                        Brand
                    </Label>
                    <Select id="brand_ref_id" v-model="form.brand_ref_id">
                      <SelectTrigger>
                        <SelectValue placeholder="Select a brand" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectGroup>
                          <SelectItem value="1">
                            Brand 1
                          </SelectItem>
                          <SelectItem value="2">
                            Brand 2
                          </SelectItem>
                        </SelectGroup>
                      </SelectContent>
                    </Select>
                  </div>
                  <div class="grid grid-cols-2 gap-2">
                    <div class="grid items-center gap-2">
                      <Label for="price" class="">
                        Price
                      </Label>
                      <Input type="number" v-model="form.price" id="price" min='0' placeholder="Type price here."/>
                    </div>
                    <div class="grid items-center gap-2">
                      <Label for="discount_price" class="">
                        Discount Price
                      </Label>
                      <Input type="number" v-model="form.discount_price" id="price" min='0' placeholder="Type discount price here."/>
                    </div>
                  </div>
              </div>

              <div class="grid gap-4 py-4">
                  <div class="flex items-center">
                      <input 
                        type="file" 
                        id="fileInput" 
                        @change="handleFileChange" 
                        class="hidden" 
                      />
                      <label 
                        for="fileInput" 
                        class="bg-slate-700 text-white rounded-md px-2 py-1 text-sm cursor-pointer hover:bg-slate-600 transition duration-200"
                      >
                        Upload File
                      </label>
                      <img
                        v-if="imagePreviewUrl"
                        :src="imagePreviewUrl"
                        class="w-20 h-20 mt-4 rounded-md border border-gray-400"
                        alt="Image preview"
                      />
                  </div>

                  <div class="grid items-center gap-2">
                    <Label for="name" class="">
                      Benefits content
                    </Label>
                    <Textarea v-model="form.benefits_content" placeholder="Type your message here." rows="3"/>
                  </div>
                  <div class="grid items-center gap-2">
                    <Label for="title" class="">
                      Ingredients content
                    </Label>
                    <Textarea v-model="form.ingredients_content" placeholder="Type your message here." rows="3" />
                  </div>
                  <div class="grid items-center gap-2">
                    <Label for="description" class="">
                      Howtouse content
                    </Label>
                    <Textarea v-model="form.howtouse_content" placeholder="Type your message here." rows="3" />
                  </div>
                  <div class="grid items-center gap-2">
                    <Label for="product_size" class="">
                      Product Size
                    </Label>
                    <TagsInput v-model="productSizeValue">
                      <TagsInputItem v-for="item in productSizeValue" :key="item" :value="item">
                        <TagsInputItemText />
                        <TagsInputItemDelete />
                      </TagsInputItem>
                      <TagsInputInput placeholder="product size..." />
                    </TagsInput>
                  </div>
                  <div class="flex items-center space-x-2">
                    <Checkbox id="is_active" v-model="form.is_active" @click="form.is_active = !form.is_active" :checked="form.is_active == 1" />
                    <label
                      for="is_active"
                      class="text-md leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                    >
                      Is Active?
                    </label>
                  </div>
              </div>
            </div>
            <DialogFooter>
                <Button @click="submit">
                  {{ isEditing ? 'Update' : 'Create' }}
                </Button>
            </DialogFooter>
            </DialogContent>
        </Dialog>
    </Layout>
</template>